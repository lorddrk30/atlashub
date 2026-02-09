<?php

namespace App\Domain\Documents\Services;

use App\Models\Document;
use App\Models\System;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    public function list(array $filters): LengthAwarePaginator
    {
        $query = Document::query()
            ->with([
                'system:id,name,slug',
                'module:id,system_id,name,slug',
                'endpoint:id,public_id,module_id,name,method,path',
                'uploader:id,name,email',
            ])
            ->where('system_id', (int) $filters['system_id']);

        if (! empty($filters['module_id'])) {
            $query->where('module_id', (int) $filters['module_id']);
        }

        if (! empty($filters['endpoint_id'])) {
            $query->where('endpoint_id', (int) $filters['endpoint_id']);
        }

        if (! empty($filters['q'])) {
            $needle = mb_strtolower((string) $filters['q']);
            $query->where(function (Builder $builder) use ($needle): void {
                $builder
                    ->whereRaw('LOWER(title) LIKE ?', ["%{$needle}%"])
                    ->orWhereRaw('LOWER(description) LIKE ?', ["%{$needle}%"]);
            });
        }

        return $query->latest()->paginate(20);
    }

    public function store(array $data, UploadedFile $file, User $user): Document
    {
        $system = System::query()->select('id', 'slug')->findOrFail((int) $data['system_id']);
        $safeFileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'document';
        $fileName = sprintf('%s-%s.pdf', now()->format('YmdHis'), Str::limit($safeFileName, 80, ''));
        $directory = sprintf('documents/%s', $system->slug);
        $path = $file->storeAs($directory, $fileName, 'public');

        return Document::query()->create([
            'system_id' => $system->id,
            'module_id' => $data['module_id'] ?? null,
            'endpoint_id' => $data['endpoint_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType() ?: 'application/pdf',
            'size' => $file->getSize() ?: 0,
            'uploaded_by' => $user->id,
        ]);
    }

    public function delete(Document $document): void
    {
        if (filled($document->file_path) && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
    }
}
