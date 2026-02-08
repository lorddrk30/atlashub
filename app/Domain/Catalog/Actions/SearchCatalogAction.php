<?php

namespace App\Domain\Catalog\Actions;

use App\Domain\Catalog\DTOs\SearchFiltersData;
use App\Domain\Catalog\Services\CatalogSearchService;

class SearchCatalogAction
{
    public function __construct(private readonly CatalogSearchService $service)
    {
    }

    public function execute(SearchFiltersData $filters): array
    {
        return $this->service->execute($filters);
    }
}
