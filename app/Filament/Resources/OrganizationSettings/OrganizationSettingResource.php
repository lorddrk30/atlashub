<?php

namespace App\Filament\Resources\OrganizationSettings;

use App\Filament\Resources\OrganizationSettings\Pages\CreateOrganizationSetting;
use App\Filament\Resources\OrganizationSettings\Pages\EditOrganizationSetting;
use App\Filament\Resources\OrganizationSettings\Pages\ListOrganizationSettings;
use App\Filament\Resources\OrganizationSettings\Pages\ViewOrganizationSetting;
use App\Filament\Resources\OrganizationSettings\Schemas\OrganizationSettingForm;
use App\Filament\Resources\OrganizationSettings\Schemas\OrganizationSettingInfolist;
use App\Filament\Resources\OrganizationSettings\Tables\OrganizationSettingsTable;
use App\Models\OrganizationSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizationSettingResource extends Resource
{
    protected static ?string $model = OrganizationSetting::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string | \UnitEnum | null $navigationGroup = 'Configuracion';

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string
    {
        return 'Configuracion de organizacion';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Configuracion de organizacion';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('organization.manage') ?? false;
    }

    public static function canCreate(): bool
    {
        $canManage = auth()->user()?->can('organization.manage') ?? false;

        return $canManage && OrganizationSetting::query()->count() === 0;
    }

    public static function form(Schema $schema): Schema
    {
        return OrganizationSettingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrganizationSettingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizationSettings::route('/'),
            'create' => CreateOrganizationSetting::route('/create'),
            'view' => ViewOrganizationSetting::route('/{record}'),
            'edit' => EditOrganizationSetting::route('/{record}/edit'),
        ];
    }
}

