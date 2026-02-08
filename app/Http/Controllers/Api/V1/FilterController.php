<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Catalog\Repositories\CatalogSearchRepository;
use App\Http\Controllers\Controller;

class FilterController extends Controller
{
    public function __invoke(CatalogSearchRepository $repository)
    {
        return response()->json($repository->filterData());
    }
}
