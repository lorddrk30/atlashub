<?php

namespace App\Filament\Resources\Endpoints;

use App\Filament\Resources\Endpoints\Pages\CreateEndpoint;
use App\Filament\Resources\Endpoints\Pages\EditEndpoint;
use App\Filament\Resources\Endpoints\Pages\ListEndpoints;
use App\Filament\Resources\Endpoints\Pages\ViewEndpoint;
use App\Filament\Resources\Endpoints\Schemas\EndpointForm;
use App\Filament\Resources\Endpoints\Schemas\EndpointInfolist;
use App\Filament\Resources\Endpoints\Tables\EndpointsTable;
use App\Models\Endpoint;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EndpointResource extends Resource
{
    protected static ?string $model = Endpoint::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracketSquare;

    protected static string|\UnitEnum|null $navigationGroup = 'Catalogo API';

    protected static ?int $navigationSort = 3;

    public static function getModelLabel(): string
    {
        return 'Endpoint';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Endpoints';
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user
            ? ($user->can('endpoint.manage') || $user->can('endpoint.publish'))
            : false;
    }

    public static function form(Schema $schema): Schema
    {
        return EndpointForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EndpointInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EndpointsTable::configure($table);
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
            'index' => ListEndpoints::route('/'),
            'create' => CreateEndpoint::route('/create'),
            'view' => ViewEndpoint::route('/{record}'),
            'edit' => EditEndpoint::route('/{record}/edit'),
        ];
    }
}
