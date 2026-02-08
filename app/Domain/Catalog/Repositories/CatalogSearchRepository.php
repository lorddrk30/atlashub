<?php

namespace App\Domain\Catalog\Repositories;

use App\Domain\Catalog\DTOs\SearchFiltersData;
use App\Models\Artefact;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use Illuminate\Support\Collection;

class CatalogSearchRepository
{
    public function searchSystems(SearchFiltersData $filters): Collection
    {
        $query = System::query()->select(['id', 'name', 'slug', 'description']);

        if ($filters->systemId) {
            $query->where('id', $filters->systemId);
        }

        if ($filters->query) {
            $needle = mb_strtolower($filters->query);
            $query->where(function ($subQuery) use ($needle): void {
                $subQuery
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"]);
            });
        }

        return $query->orderBy('name')->limit($filters->perCategory)->get();
    }

    public function searchModules(SearchFiltersData $filters): Collection
    {
        $query = Module::query()
            ->select(['id', 'system_id', 'name', 'slug', 'description'])
            ->with('system:id,name,slug');

        if ($filters->systemId) {
            $query->where('system_id', $filters->systemId);
        }

        if ($filters->moduleId) {
            $query->where('id', $filters->moduleId);
        }

        if ($filters->query) {
            $needle = mb_strtolower($filters->query);
            $query->where(function ($subQuery) use ($needle): void {
                $subQuery
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"])
                    ->orWhereHas('system', function ($systemQuery) use ($needle): void {
                        $systemQuery
                            ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                            ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$needle}%"])
                            ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"]);
                    });
            });
        }

        return $query->orderBy('name')->limit($filters->perCategory)->get();
    }

    public function searchEndpoints(SearchFiltersData $filters): Collection
    {
        $query = Endpoint::query()
            ->select([
                'id',
                'module_id',
                'name',
                'method',
                'path',
                'description',
                'authentication_type',
                'status',
                'updated_at',
            ])
            ->with(['module:id,system_id,name,slug', 'module.system:id,name,slug'])
            ->where('status', 'published');

        if ($filters->moduleId) {
            $query->where('module_id', $filters->moduleId);
        }

        if ($filters->systemId) {
            $query->whereHas('module', fn ($subQuery) => $subQuery->where('system_id', $filters->systemId));
        }

        if ($filters->method) {
            $query->where('method', $filters->method);
        }

        if ($filters->authenticationType) {
            $query->where('authentication_type', $filters->authenticationType);
        }

        if ($filters->query) {
            $needle = mb_strtolower($filters->query);
            $query->where(function ($subQuery) use ($needle): void {
                $subQuery
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(path) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(method) LIKE ?', ["%{$needle}%"])
                    ->orWhereHas('module', function ($moduleQuery) use ($needle): void {
                        $moduleQuery
                            ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                            ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$needle}%"])
                            ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"])
                            ->orWhereHas('system', function ($systemQuery) use ($needle): void {
                                $systemQuery
                                    ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                                    ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$needle}%"])
                                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"]);
                            });
                    });
            });
        }

        return $query
            ->orderByDesc('updated_at')
            ->limit($filters->perCategory)
            ->get();
    }

    public function searchArtefacts(SearchFiltersData $filters): Collection
    {
        $query = Artefact::query()
            ->select([
                'id',
                'system_id',
                'module_id',
                'endpoint_id',
                'type',
                'title',
                'url',
                'description',
                'updated_at',
            ])
            ->with(['system:id,name,slug', 'module:id,name,slug', 'endpoint:id,name,method,path']);

        if ($filters->artefactType) {
            $query->where('type', $filters->artefactType);
        }

        if ($filters->systemId) {
            $query->where(function ($subQuery) use ($filters): void {
                $subQuery
                    ->where('system_id', $filters->systemId)
                    ->orWhereHas('module', fn ($moduleQuery) => $moduleQuery->where('system_id', $filters->systemId))
                    ->orWhereHas('endpoint.module', fn ($moduleQuery) => $moduleQuery->where('system_id', $filters->systemId));
            });
        }

        if ($filters->moduleId) {
            $query->where(function ($subQuery) use ($filters): void {
                $subQuery
                    ->where('module_id', $filters->moduleId)
                    ->orWhereHas('endpoint', fn ($endpointQuery) => $endpointQuery->where('module_id', $filters->moduleId));
            });
        }

        if ($filters->query) {
            $needle = mb_strtolower($filters->query);
            $query->where(function ($subQuery) use ($needle): void {
                $subQuery
                    ->whereRaw('LOWER(title) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(url) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(type) LIKE ?', ["%{$needle}%"])
                    ->orWhereHas('system', function ($systemQuery) use ($needle): void {
                        $systemQuery
                            ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                            ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$needle}%"]);
                    })
                    ->orWhereHas('module', function ($moduleQuery) use ($needle): void {
                        $moduleQuery
                            ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                            ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$needle}%"]);
                    })
                    ->orWhereHas('endpoint', function ($endpointQuery) use ($needle): void {
                        $endpointQuery
                            ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                            ->orWhereRaw('LOWER(path) LIKE ?', ["%{$needle}%"])
                            ->orWhereRaw('LOWER(method) LIKE ?', ["%{$needle}%"]);
                    });
            });
        }

        return $query
            ->orderByDesc('updated_at')
            ->limit($filters->perCategory)
            ->get();
    }

    public function findEndpointById(int $endpointId): ?Endpoint
    {
        return Endpoint::query()
            ->with(['module:id,system_id,name,slug,description', 'module.system:id,name,slug,description', 'artefacts'])
            ->find($endpointId);
    }

    public function filterData(): array
    {
        return [
            'systems' => System::query()->select('id', 'name', 'slug')->orderBy('name')->get(),
            'modules' => Module::query()->select('id', 'system_id', 'name', 'slug')->orderBy('name')->get(),
            'methods' => Endpoint::METHODS,
            'authentication_types' => Endpoint::AUTH_TYPES,
            'artefact_types' => Artefact::TYPES,
        ];
    }
}
