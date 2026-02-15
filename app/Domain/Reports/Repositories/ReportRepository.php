<?php

namespace App\Domain\Reports\Repositories;

use App\Domain\Reports\DTOs\ReportFiltersData;
use App\Models\Artefact;
use App\Models\Endpoint;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public function summary(ReportFiltersData $filters): array
    {
        $endpointQuery = $this->endpointBaseQuery($filters);
        $artefactQuery = $this->artefactBaseQuery($filters);
        $systemsQuery = $this->systemsBaseQuery($filters);

        return [
            'kpis' => [
                'systems' => (clone $systemsQuery)->count(),
                'modules' => (clone $endpointQuery)->distinct('m.id')->count('m.id'),
                'endpoints' => (clone $endpointQuery)->count('e.id'),
                'artefacts' => (clone $artefactQuery)->count('a.id'),
            ],
            'charts' => [
                'systems_by_status' => $this->systemsByStatus($filters),
                'endpoints_by_system' => $this->endpointsBySystem($endpointQuery),
                'endpoints_by_module' => $this->endpointsByModule($endpointQuery),
                'artefacts_by_type' => $this->artefactsByType($artefactQuery),
                'endpoints_by_method' => $this->endpointsByMethod($endpointQuery),
            ],
            'tables' => [
                'systems' => $this->systemsTable($systemsQuery),
                'modules' => $this->modulesTable($endpointQuery),
                'endpoints' => $this->endpointsTable($endpointQuery),
                'artefacts' => $this->artefactsTable($artefactQuery),
            ],
            'filters' => $this->filterCatalog(),
        ];
    }

    private function endpointBaseQuery(ReportFiltersData $filters): Builder
    {
        $query = DB::table('endpoints as e')
            ->join('modules as m', 'm.id', '=', 'e.module_id')
            ->join('systems as s', 's.id', '=', 'm.system_id');

        if ($filters->systemId) {
            $query->where('m.system_id', $filters->systemId);
        }

        if ($filters->moduleId) {
            $query->where('e.module_id', $filters->moduleId);
        }

        if ($filters->systemStatus) {
            $query->where('s.status', $filters->systemStatus);
        }

        if ($filters->resolvedEndpointStatuses()) {
            $query->whereIn('e.status', $filters->resolvedEndpointStatuses());
        }

        if ($filters->dateFrom) {
            $query->where('e.created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo) {
            $query->where('e.created_at', '<=', $filters->dateTo);
        }

        return $query;
    }

    private function artefactBaseQuery(ReportFiltersData $filters): Builder
    {
        $query = DB::table('artefacts as a')
            ->leftJoin('endpoints as e', 'e.id', '=', 'a.endpoint_id')
            ->leftJoin('modules as m', DB::raw('m.id'), '=', DB::raw('COALESCE(a.module_id, e.module_id)'))
            ->leftJoin('systems as s', DB::raw('s.id'), '=', DB::raw('COALESCE(a.system_id, m.system_id)'));

        if ($filters->systemId) {
            $query->whereRaw('COALESCE(a.system_id, m.system_id) = ?', [$filters->systemId]);
        }

        if ($filters->moduleId) {
            $query->whereRaw('COALESCE(a.module_id, e.module_id) = ?', [$filters->moduleId]);
        }

        if ($filters->systemStatus) {
            $query->where('s.status', $filters->systemStatus);
        }

        if ($filters->resolvedEndpointStatuses()) {
            $query->whereIn('e.status', $filters->resolvedEndpointStatuses());
        }

        if ($filters->dateFrom) {
            $query->where('a.created_at', '>=', $filters->dateFrom);
        }

        if ($filters->dateTo) {
            $query->where('a.created_at', '<=', $filters->dateTo);
        }

        return $query;
    }

    private function systemsBaseQuery(ReportFiltersData $filters): Builder
    {
        $query = DB::table('systems as s')
            ->select('s.id', 's.name', 's.slug', 's.status', 's.updated_at');

        if ($filters->systemId) {
            $query->where('s.id', $filters->systemId);
        }

        if ($filters->moduleId) {
            $query->whereExists(function ($subQuery) use ($filters): void {
                $subQuery
                    ->selectRaw('1')
                    ->from('modules as m')
                    ->whereColumn('m.system_id', 's.id')
                    ->where('m.id', $filters->moduleId);
            });
        }

        if ($filters->systemStatus) {
            $query->where('s.status', $filters->systemStatus);
        }

        return $query;
    }

    private function systemsByStatus(ReportFiltersData $filters): array
    {
        $rows = (clone $this->systemsBaseQuery($filters))
            ->select('s.status', DB::raw('COUNT(s.id) as total'))
            ->groupBy('s.status')
            ->pluck('total', 'status');

        return collect([
            'draft' => 'Borrador',
            'published' => 'Publicado',
            'discarded' => 'Descartado',
        ])
            ->map(fn (string $label, string $status): array => [
                'label' => $label,
                'value' => (int) ($rows[$status] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function endpointsBySystem(Builder $query): array
    {
        return (clone $query)
            ->select('s.id', 's.name', DB::raw('COUNT(e.id) as total'))
            ->groupBy('s.id', 's.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row): array => [
                'id' => (int) $row->id,
                'label' => (string) $row->name,
                'value' => (int) $row->total,
            ])
            ->values()
            ->all();
    }

    private function endpointsByModule(Builder $query): array
    {
        return (clone $query)
            ->select('m.id', 'm.name', 's.name as system_name', DB::raw('COUNT(e.id) as total'))
            ->groupBy('m.id', 'm.name', 's.name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($row): array => [
                'id' => (int) $row->id,
                'label' => (string) $row->name,
                'system' => (string) $row->system_name,
                'value' => (int) $row->total,
            ])
            ->values()
            ->all();
    }

    private function artefactsByType(Builder $query): array
    {
        $rows = (clone $query)
            ->select('a.type', DB::raw('COUNT(a.id) as total'))
            ->groupBy('a.type')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn ($row): array => [(string) $row->type => (int) $row->total]);

        return collect(Artefact::TYPES)
            ->map(fn (string $type): array => [
                'label' => $type,
                'value' => (int) ($rows[$type] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function endpointsByMethod(Builder $query): array
    {
        $rows = (clone $query)
            ->select('e.method', DB::raw('COUNT(e.id) as total'))
            ->groupBy('e.method')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn ($row): array => [(string) $row->method => (int) $row->total]);

        return collect(Endpoint::METHODS)
            ->map(fn (string $method): array => [
                'label' => $method,
                'value' => (int) ($rows[$method] ?? 0),
            ])
            ->values()
            ->all();
    }

    private function systemsTable(Builder $query): array
    {
        return (clone $query)
            ->leftJoin('modules as m', 'm.system_id', '=', 's.id')
            ->leftJoin('endpoints as e', function ($join): void {
                $join->on('e.module_id', '=', 'm.id');
            })
            ->select('s.id', 's.name', 's.slug', 's.status', DB::raw('COUNT(e.id) as endpoint_count'))
            ->groupBy('s.id', 's.name', 's.slug', 's.status')
            ->orderBy('s.name')
            ->get()
            ->map(fn ($row): array => [
                'id' => (int) $row->id,
                'name' => (string) $row->name,
                'slug' => (string) $row->slug,
                'status' => (string) $row->status,
                'endpoint_count' => (int) $row->endpoint_count,
            ])
            ->values()
            ->all();
    }

    private function modulesTable(Builder $query): array
    {
        return (clone $query)
            ->select('m.id', 'm.name', 's.name as system_name', DB::raw('COUNT(e.id) as endpoint_count'), DB::raw('MAX(e.updated_at) as last_update'))
            ->groupBy('m.id', 'm.name', 's.name')
            ->orderByDesc('endpoint_count')
            ->get()
            ->map(fn ($row): array => [
                'id' => (int) $row->id,
                'name' => (string) $row->name,
                'system_name' => (string) $row->system_name,
                'endpoint_count' => (int) $row->endpoint_count,
                'last_update' => $row->last_update ? (string) $row->last_update : null,
            ])
            ->values()
            ->all();
    }

    private function endpointsTable(Builder $query): array
    {
        return (clone $query)
            ->select('e.public_id', 'e.name', 'e.method', 'e.path', 'e.status', 'm.name as module_name', 's.name as system_name', 'e.updated_at')
            ->orderByDesc('e.updated_at')
            ->limit(150)
            ->get()
            ->map(fn ($row): array => [
                'public_id' => (string) $row->public_id,
                'name' => (string) $row->name,
                'method' => (string) $row->method,
                'path' => (string) $row->path,
                'status' => (string) $row->status,
                'module_name' => (string) $row->module_name,
                'system_name' => (string) $row->system_name,
                'updated_at' => $row->updated_at ? (string) $row->updated_at : null,
            ])
            ->values()
            ->all();
    }

    private function artefactsTable(Builder $query): array
    {
        return (clone $query)
            ->select('a.id', 'a.type', 'a.title', 'a.url', 'e.path as endpoint_path', 'm.name as module_name', 's.name as system_name', 'a.updated_at')
            ->orderByDesc('a.updated_at')
            ->limit(150)
            ->get()
            ->map(fn ($row): array => [
                'id' => (int) $row->id,
                'type' => (string) $row->type,
                'title' => (string) $row->title,
                'url' => (string) $row->url,
                'endpoint_path' => $row->endpoint_path ? (string) $row->endpoint_path : null,
                'module_name' => $row->module_name ? (string) $row->module_name : null,
                'system_name' => $row->system_name ? (string) $row->system_name : null,
                'updated_at' => $row->updated_at ? (string) $row->updated_at : null,
            ])
            ->values()
            ->all();
    }

    private function filterCatalog(): array
    {
        return [
            'systems' => DB::table('systems')->select('id', 'name')->orderBy('name')->get()->toArray(),
            'modules' => DB::table('modules')->select('id', 'system_id', 'name')->orderBy('name')->get()->toArray(),
            'system_statuses' => [
                ['value' => 'draft', 'label' => 'Borrador'],
                ['value' => 'published', 'label' => 'Publicado'],
                ['value' => 'discarded', 'label' => 'Descartado'],
            ],
            'statuses' => [
                ['value' => 'active', 'label' => 'Activo'],
                ['value' => 'deprecated', 'label' => 'Deprecado'],
                ['value' => 'archived', 'label' => 'Archivado'],
            ],
        ];
    }
}
