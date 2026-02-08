<?php

namespace App\Filament\Resources\Modules\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ModuleInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Nombre'),
                TextEntry::make('slug'),
                TextEntry::make('system.name')->label('Sistema'),
                TextEntry::make('status')->badge(),
                TextEntry::make('description')->label('Descripcion')->placeholder('-'),
                TextEntry::make('updated_at')->label('Actualizado')->dateTime()->placeholder('-'),
            ]);
    }
}
