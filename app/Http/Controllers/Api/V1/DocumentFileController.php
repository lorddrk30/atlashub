<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class DocumentFileController extends Controller
{
    public function __invoke(Request $request, Document $document): Response
    {
        $this->authorize('view', $document);

        abort_unless(Storage::disk('public')->exists($document->file_path), 404, 'Archivo no encontrado.');

        $content = Storage::disk('public')->get($document->file_path);
        $filename = sprintf('%s.pdf', str($document->title)->slug());
        $disposition = $request->boolean('download') ? 'attachment' : 'inline';

        return response($content, 200, [
            'Content-Type' => $document->mime_type ?: 'application/pdf',
            'Content-Length' => (string) $document->size,
            'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
            'Cache-Control' => 'private, max-age=0, must-revalidate',
        ]);
    }
}
