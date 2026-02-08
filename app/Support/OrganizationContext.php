<?php

namespace App\Support;

use App\Models\OrganizationSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class OrganizationContext
{
    public static function config(): array
    {
        $defaults = [
            'name' => (string) config('organization.default_name', 'RikarCoffe'),
            'short_name' => (string) config('organization.default_short_name', 'RC'),
            'slug' => (string) config('organization.default_slug', 'rikarcoffe'),
            'tagline' => (string) config('organization.default_tagline', 'Portal interno para APIs y artefactos tecnicos'),
            'description' => (string) config('organization.default_description', ''),
            'logo_url' => config('organization.default_logo_url'),
            'favicon_url' => config('organization.default_favicon_url'),
            'support_email' => (string) config('organization.default_support_email', ''),
            'primary_color' => (string) config('organization.default_primary_color', '#22d3ee'),
            'secondary_color' => (string) config('organization.default_secondary_color', '#34d399'),
        ];

        if (! Schema::hasTable('organization_settings')) {
            return $defaults;
        }

        $setting = OrganizationSetting::query()->first();

        if (! $setting) {
            return $defaults;
        }

        return [
            'name' => $setting->name ?: $defaults['name'],
            'short_name' => $setting->short_name ?: $defaults['short_name'],
            'slug' => $setting->slug ?: $defaults['slug'],
            'tagline' => $setting->tagline ?: $defaults['tagline'],
            'description' => $setting->description ?: $defaults['description'],
            'logo_url' => $setting->logo ? Storage::disk('public')->url($setting->logo) : $defaults['logo_url'],
            'favicon_url' => $setting->favicon ? Storage::disk('public')->url($setting->favicon) : $defaults['favicon_url'],
            'support_email' => $setting->support_email ?: $defaults['support_email'],
            'primary_color' => $setting->primary_color ?: $defaults['primary_color'],
            'secondary_color' => $setting->secondary_color ?: $defaults['secondary_color'],
        ];
    }

    public static function portalConfig(): array
    {
        $config = static::config();

        return [
            ...$config,
            'backoffice_url' => '/backoffice',
            'portal_title' => "{$config['name']} | AtlasHub",
        ];
    }

    public static function backofficeBrandName(): string
    {
        $config = static::config();

        return "{$config['name']} Backoffice";
    }

    public static function backofficeLogoHtml(): ?HtmlString
    {
        $logoUrl = static::config()['logo_url'] ?? null;

        if (! filled($logoUrl)) {
            return null;
        }

        return new HtmlString('<img src="'.e($logoUrl).'" alt="'.e(static::config()['name']).'" style="height: 3rem; max-height: 60px; width: auto; max-width: 100%; object-fit: contain;" />');
    }
}

