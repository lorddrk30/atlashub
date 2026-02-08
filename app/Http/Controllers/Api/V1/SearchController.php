<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Catalog\Actions\SearchCatalogAction;
use App\Domain\Catalog\DTOs\SearchFiltersData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SearchRequest;

class SearchController extends Controller
{
    public function __invoke(SearchRequest $request, SearchCatalogAction $action)
    {
        $result = $action->execute(SearchFiltersData::fromRequest($request));

        return response()->json($result);
    }
}
