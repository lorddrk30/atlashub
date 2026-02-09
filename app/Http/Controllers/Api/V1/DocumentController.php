<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Documents\Services\DocumentService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListDocumentsRequest;
use App\Http\Requests\Api\V1\StoreDocumentRequest;
use App\Models\Document;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $service)
    {
    }

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $this->authorize('create', Document::class);

        $document = $this->service->store(
            data: $request->validated(),
            file: $request->file('file'),
            user: $request->user()
        );

        return response()->json([
            'item' => $this->transformDocument($document->load(['system:id,name,slug', 'module:id,name,slug', 'endpoint:id,public_id,name,method,path'])),
        ], 201);
    }

    public function index(ListDocumentsRequest $request): JsonResponse
    {
        $documents = $this->service->list($request->validated());

        return response()->json([
            'items' => collect($documents->items())->map(fn (Document $document): array => $this->transformDocument($document))->values(),
            'meta' => [
                'current_page' => $documents->currentPage(),
                'per_page' => $documents->perPage(),
                'total' => $documents->total(),
            ],
        ]);
    }

    public function show(Document $document): JsonResponse
    {
        return response()->json([
            'item' => $this->transformDocument($document->load([
                'system:id,name,slug',
                'module:id,system_id,name,slug',
                'endpoint:id,public_id,module_id,name,method,path',
                'uploader:id,name,email',
            ])),
        ]);
    }

    public function destroy(Document $document): JsonResponse
    {
        $this->authorize('delete', $document);

        $this->service->delete($document);

        return response()->json(status: 204);
    }

    private function transformDocument(Document $document): array
    {
        $payload = $document->toArray();
        $payload['view_url'] = route('documents.file', ['document' => $document->id]);
        $payload['download_url'] = route('documents.file', ['document' => $document->id, 'download' => 1]);

        return $payload;
    }
}
