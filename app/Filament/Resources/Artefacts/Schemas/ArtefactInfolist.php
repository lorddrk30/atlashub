<?php

namespace App\Filament\Resources\Artefacts\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ArtefactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')->label('Titulo'),
                TextEntry::make('type')->label('Tipo')->badge(),
                TextEntry::make('url')->label('URL')->url(fn ($state): string => (string) $state, shouldOpenInNewTab: true),
                TextEntry::make('description')->label('Descripcion')->placeholder('-'),
                TextEntry::make('system.name')->label('Sistema')->placeholder('-'),
                TextEntry::make('module.name')->label('Modulo')->placeholder('-'),
                TextEntry::make('endpoint.name')->label('Endpoint')->placeholder('-'),
                KeyValueEntry::make('metadata')->label('Metadata')->placeholder('-'),
                TextEntry::make('updated_at')->label('Actualizado')->dateTime()->placeholder('-'),
            ]);
    }
}
