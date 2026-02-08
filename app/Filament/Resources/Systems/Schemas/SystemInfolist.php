<?php

namespace App\Filament\Resources\Systems\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SystemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Nombre'),
                TextEntry::make('slug'),
                TextEntry::make('description')->label('Descripcion')->placeholder('-'),
                TextEntry::make('updated_at')->label('Actualizado')->dateTime()->placeholder('-'),
            ]);
    }
}
