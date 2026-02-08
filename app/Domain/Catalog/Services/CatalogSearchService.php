<?php

namespace App\Domain\Catalog\Services;

use App\Domain\Catalog\DTOs\SearchFiltersData;
use App\Domain\Catalog\Repositories\CatalogSearchRepository;

class CatalogSearchService
{
    public function __construct(private readonly CatalogSearchRepository $repository)
    {
    }

    public function execute(SearchFiltersData $filters): array
    {
        $systems = $this->repository->searchSystems($filters);
        $modules = $this->repository->searchModules($filters);
        $endpoints = $this->repository->searchEndpoints($filters);
        $artefacts = $this->repository->searchArtefacts($filters);

        return [
            'query' => $filters->query,
            'total' => $systems->count() + $modules->count() + $endpoints->count() + $artefacts->count(),
            'counts' => [
                'systems' => $systems->count(),
                'modules' => $modules->count(),
                'endpoints' => $endpoints->count(),
                'artefacts' => $artefacts->count(),
            ],
            'grouped' => [
                'systems' => $systems,
                'modules' => $modules,
                'endpoints' => $endpoints,
                'artefacts' => $artefacts,
            ],
        ];
    }
}
