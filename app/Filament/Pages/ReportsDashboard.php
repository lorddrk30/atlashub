<?php

namespace App\Filament\Pages;

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

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'editor']) ?? false;
    }

    public function getTitle(): string
    {
        return 'Reportes AtlasHub';
    }
}
