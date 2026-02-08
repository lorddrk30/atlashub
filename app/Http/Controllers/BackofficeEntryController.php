<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BackofficeEntryController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/admin/login');
        }

        if ($user->hasAnyRole(['admin', 'editor'])) {
            return redirect('/admin');
        }

        return redirect()->route('backoffice.forbidden');
    }

    public function forbidden(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/admin/login');
        }

        if ($user->hasAnyRole(['admin', 'editor'])) {
            return redirect('/admin');
        }

        return view('backoffice-forbidden', [
            'roles' => $user->getRoleNames()->values(),
        ]);
    }
}

