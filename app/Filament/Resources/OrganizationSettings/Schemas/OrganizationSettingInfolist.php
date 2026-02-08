<?php

namespace App\Filament\Resources\OrganizationSettings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrganizationSettingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Nombre'),
                TextEntry::make('short_name')->label('Nombre corto'),
                TextEntry::make('slug')->label('Slug'),
                TextEntry::make('tagline')->label('Tagline')->placeholder('-'),
                TextEntry::make('description')->label('Descripcion')->placeholder('-'),
                TextEntry::make('logo')->label('Logo')->placeholder('-'),
                TextEntry::make('favicon')->label('Favicon')->placeholder('-'),
                TextEntry::make('support_email')->label('Soporte')->placeholder('-'),
                TextEntry::make('primary_color')->label('Color primario'),
                TextEntry::make('secondary_color')->label('Color secundario'),
                TextEntry::make('updated_at')->label('Actualizado')->dateTime(),
            ]);
    }
}
