<?php

namespace App\Domain\Catalog\Actions;

use App\Domain\Catalog\Repositories\CatalogSearchRepository;
use App\Models\Endpoint;

class GetEndpointDetailAction
{
    public function __construct(private readonly CatalogSearchRepository $repository)
    {
    }

    public function execute(int $endpointId): ?Endpoint
    {
        return $this->repository->findEndpointById($endpointId);
    }
}
