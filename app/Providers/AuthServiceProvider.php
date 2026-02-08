<?php

namespace App\Providers;

use App\Models\Artefact;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\OrganizationSetting;
use App\Models\System;
use App\Policies\ArtefactPolicy;
use App\Policies\EndpointPolicy;
use App\Policies\ModulePolicy;
use App\Policies\OrganizationSettingPolicy;
use App\Policies\SystemPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Artefact::class => ArtefactPolicy::class,
        Endpoint::class => EndpointPolicy::class,
        Module::class => ModulePolicy::class,
        OrganizationSetting::class => OrganizationSettingPolicy::class,
        System::class => SystemPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
