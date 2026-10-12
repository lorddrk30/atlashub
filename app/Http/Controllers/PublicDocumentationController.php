<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Support\OrganizationContext;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PublicDocumentationController extends Controller
{
    public function __invoke(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:120']]);
        $search = trim($filters['q'] ?? '');

        $documents = Document::query()
            ->select(['id', 'title', 'description', 'type', 'size'])
            ->where('is_public', true)
            ->when($search !== '', function (Builder $query) use ($search): void {
                $term = mb_strtolower($search);

                $query->where(function (Builder $query) use ($term): void {
                    $query->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(description) LIKE ?', ["%{$term}%"]);
                });
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('documentation.index', [
            'documents' => $documents,
            'search' => $search,
            'organization' => OrganizationContext::config(),
        ]);
    }
}
