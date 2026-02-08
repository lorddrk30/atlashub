<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Support\OrganizationContext;

class PortalController extends Controller
{
    public function __invoke()
    {
        return view('portal', [
            'portalConfig' => OrganizationContext::portalConfig(),
        ]);
    }
}
