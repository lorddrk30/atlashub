<?php

namespace App\Filament\Pages;

use App\Models\Artefact;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\System;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ReportsDashboard extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?string $navigationLabel = 'Reportes';

    protected static string|\UnitEnum|null $navigationGroup = 'Analitica';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.reports-dashboard';

    protected static ?string $slug = 'reports';

    public array $stats = [];

    public function mount(): void
    {
        $this->stats = [
            [
                'label' => 'Sistemas',
                'value' => (string) System::query()->count(),
            ],
            [
                'label' => 'Modulos',
                'value' => (string) Module::query()->count(),
            ],
            [
                'label' => 'Endpoints',
                'value' => (string) Endpoint::query()->count(),
            ],
            [
                'label' => 'Artefactos',
                'value' => (string) Artefact::query()->count(),
            ],
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }

    public function getTitle(): string
    {
        return 'Reportes AtlasHub';
    }
}
