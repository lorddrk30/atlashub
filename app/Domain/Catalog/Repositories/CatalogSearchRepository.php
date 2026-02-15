<?php

namespace App\Domain\Catalog\Repositories;

use App\Domain\Catalog\DTOs\SearchFiltersData;
use App\Models\Artefact;
use App\Models\Document;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CatalogSearchRepository
{
    public function searchSystems(SearchFiltersData $filters): Collection
    {
        $query = System::query()->select([
            'id',
            'name',
            'slug',
            'description',
            'prod_server',
            'prod_server_ip',
            'uat_server',
            'uat_server_ip',
            'dev_server',
            'dev_server_ip',
            'internal_url',
            'public_url',
            'responsibles',
            'user_areas',
            'repository_url',
            'home_preview_url',
        ])->published();

        if ($filters->systemId) {
            $query->where('id', $filters->systemId);
        }

        if ($filters->query) {
            $needle = mb_strtolower($filters->query);
            $query->where(function ($subQuery) use ($needle): void {
                $subQuery
                    ->whereRaw('LOWER(name) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(prod_server) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(prod_server_ip) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(uat_server) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(uat_server_ip) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(dev_server) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(dev_server_ip) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(internal_url) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(public_url) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(COALESCE(repository_url, gitlab_url)) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(CAST(responsibles AS TEXT)) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(CAST(user_areas AS TEXT)) LIKE ?', ["%{$needle}%"]);
            });
        }

        return $query
            ->orderBy('name')
            ->limit($filters->perCategory)
            ->get()
            ->map(function (System $system): System {
                $system->home_preview_url = $this->resolveImageUrl($system->home_preview_url);

                return $system;
            });
    }

    public function searchModules(SearchFiltersData $filters): Collection
    {
        $query = Module::query()
            ->select(['id', 'system_id', 'name', 'slug', 'description'])
            ->with('system:id,name,slug')
            ->whereHas('system', fn ($systemQuery) => $systemQuery->published());

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
                'public_id',
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
            ->where('status', 'published')
            ->whereHas('module.system', fn ($systemQuery) => $systemQuery->published());

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
            ->with(['system:id,name,slug', 'module:id,name,slug', 'endpoint:id,public_id,name,method,path'])
            ->where(function ($subQuery): void {
                $subQuery
                    ->whereHas('system', fn ($systemQuery) => $systemQuery->published())
                    ->orWhereHas('module.system', fn ($systemQuery) => $systemQuery->published())
                    ->orWhereHas('endpoint.module.system', fn ($systemQuery) => $systemQuery->published());
            });

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

    public function searchDocuments(SearchFiltersData $filters): Collection
    {
        $query = Document::query()
            ->select([
                'id',
                'system_id',
                'module_id',
                'endpoint_id',
                'title',
                'description',
                'type',
                'mime_type',
                'size',
                'created_at',
                'updated_at',
            ])
            ->with([
                'system:id,name,slug',
                'module:id,system_id,name,slug',
                'endpoint:id,public_id,module_id,name,method,path',
            ])
            ->whereHas('system', fn ($systemQuery) => $systemQuery->published());

        if ($filters->systemId) {
            $query->where('system_id', $filters->systemId);
        }

        if ($filters->moduleId) {
            $query->where('module_id', $filters->moduleId);
        }

        if ($filters->query) {
            $needle = mb_strtolower($filters->query);
            $query->where(function ($subQuery) use ($needle): void {
                $subQuery
                    ->whereRaw('LOWER(title) LIKE ?', ["%{$needle}%"])
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
                            ->orWhereRaw('LOWER(path) LIKE ?', ["%{$needle}%"]);
                    });
            });
        }

        return $query->latest()->limit($filters->perCategory)->get();
    }

    public function findEndpointByPublicId(string $publicId): ?Endpoint
    {
        return Endpoint::query()
            ->with(['module:id,system_id,name,slug,description', 'module.system:id,name,slug,description', 'artefacts'])
            ->whereHas('module.system', fn ($systemQuery) => $systemQuery->published())
            ->where('public_id', $publicId)
            ->first();
    }

    public function filterData(): array
    {
        return [
            'systems' => System::query()->published()->select('id', 'name', 'slug')->orderBy('name')->get(),
            'modules' => Module::query()
                ->select('id', 'system_id', 'name', 'slug')
                ->whereHas('system', fn ($systemQuery) => $systemQuery->published())
                ->orderBy('name')
                ->get(),
            'methods' => Endpoint::METHODS,
            'authentication_types' => Endpoint::AUTH_TYPES,
            'artefact_types' => Artefact::TYPES,
            'document_types' => Document::TYPES,
        ];
    }

    private function resolveImageUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
