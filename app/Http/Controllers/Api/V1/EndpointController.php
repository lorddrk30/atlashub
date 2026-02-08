<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Catalog\Actions\GetEndpointDetailAction;
use App\Http\Controllers\Controller;

class EndpointController extends Controller
{
    public function show(string $publicId, GetEndpointDetailAction $action)
    {
        $endpoint = $action->execute($publicId);

        abort_unless($endpoint && $endpoint->status === 'published', 404, 'Endpoint no encontrado.');

        return response()->json([
            'item' => $endpoint,
        ]);
    }
}
