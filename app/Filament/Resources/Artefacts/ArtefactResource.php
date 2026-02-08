<?php

namespace App\Filament\Resources\Artefacts;

use App\Filament\Resources\Artefacts\Pages\CreateArtefact;
use App\Filament\Resources\Artefacts\Pages\EditArtefact;
use App\Filament\Resources\Artefacts\Pages\ListArtefacts;
use App\Filament\Resources\Artefacts\Pages\ViewArtefact;
use App\Filament\Resources\Artefacts\Schemas\ArtefactForm;
use App\Filament\Resources\Artefacts\Schemas\ArtefactInfolist;
use App\Filament\Resources\Artefacts\Tables\ArtefactsTable;
use App\Models\Artefact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ArtefactResource extends Resource
{
    protected static ?string $model = Artefact::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogo API';

    protected static ?int $navigationSort = 4;

    public static function getModelLabel(): string
    {
        return 'Artefacto';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Artefactos';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->can('artefact.manage') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return ArtefactForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ArtefactInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArtefactsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtefacts::route('/'),
            'create' => CreateArtefact::route('/create'),
            'view' => ViewArtefact::route('/{record}'),
            'edit' => EditArtefact::route('/{record}/edit'),
        ];
    }
}
