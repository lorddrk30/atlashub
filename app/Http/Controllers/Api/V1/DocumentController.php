<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Documents\Services\DocumentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListDocumentsRequest;
use App\Http\Requests\Api\V1\StoreDocumentRequest;
use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $service) {}

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $this->authorize('create', Document::class);

        $document = $this->service->store(
            data: $request->validated(),
            file: $request->file('file'),
            user: $request->user()
        );

        return response()->json([
            'item' => $this->transformDocument($request, $document->load(['system:id,name,slug', 'module:id,name,slug', 'endpoint:id,public_id,name,method,path'])),
        ], 201);
    }

    public function index(ListDocumentsRequest $request): JsonResponse
    {
        $documents = $this->service->list(
            $request->validated(),
            $request->user()?->can('document.view') ?? false
        );

        return response()->json([
            'items' => collect($documents->items())->map(fn (Document $document): array => $this->transformDocument($request, $document))->values(),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ]);
    }

    public function show(Request $request, Document $document): JsonResponse
    {
        if (! $document->is_public && ! $request->user()) {
            abort(404);
        }

        $this->authorize('view', $document);

        return response()->json([
            'item' => $this->transformDocument($request, $document->load([
                'system:id,name,slug',
                'module:id,system_id,name,slug',
                'endpoint:id,public_id,module_id,name,method,path',
            ])),
        ]);
    }

    public function destroy(Document $document): JsonResponse
    {
        $this->authorize('delete', $document);

        $this->service->delete($document);

        return response()->json(status: 204);
    }

    private function transformDocument(Request $request, Document $document): array
    {
        $payload = $document->only([
            'id', 'title', 'description', 'type', 'mime_type', 'size', 'is_public', 'created_at', 'updated_at',
        ]);
        $fileRoute = $request->bearerToken() ? 'api.documents.file' : 'documents.file';
        $payload['view_url'] = route($fileRoute, ['document' => $document->id]);
        $payload['download_url'] = route($fileRoute, ['document' => $document->id, 'download' => 1]);

        if ($request->user()?->can('document.view')) {
            $payload['system'] = $document->system;
            $payload['module'] = $document->module;
            $payload['endpoint'] = $document->endpoint;
        }

        return $payload;
    }
}
