<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class LogsCenter extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentMagnifyingGlass;

    protected static ?string $navigationLabel = 'Logs';

    protected static string|\UnitEnum|null $navigationGroup = 'Seguridad';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.logs-center';

    protected static ?string $slug = 'logs';

    public function getLogViewerUrl(): string
    {
        return route('log-viewer.index');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('logs.view') ?? false;
    }

    public function getTitle(): string
    {
        return 'Centro de logs';
    }
}
