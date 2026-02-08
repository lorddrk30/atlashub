<?php

namespace App\Filament\Resources\Systems\Schemas;

use Filament\Infolists\Components\ImageEntry;
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
                TextEntry::make('prod_server')->label('Servidor PROD')->placeholder('-'),
                TextEntry::make('uat_server')->label('Servidor UAT')->placeholder('-'),
                TextEntry::make('dev_server')->label('Servidor DEV')->placeholder('-'),
                TextEntry::make('internal_url')->label('Dominio interno')->placeholder('-')->url(fn ($state) => $state),
                TextEntry::make('public_url')->label('Dominio publico')->placeholder('-')->url(fn ($state) => $state),
                TextEntry::make('gitlab_url')->label('Repositorio GitLab')->placeholder('-')->url(fn ($state) => $state),
                TextEntry::make('responsibles')
                    ->label('Responsables')
                    ->formatStateUsing(fn ($state): string => is_array($state) && $state !== [] ? implode(', ', $state) : '-')
                    ->placeholder('-'),
                TextEntry::make('user_areas')
                    ->label('Areas usuarias')
                    ->formatStateUsing(fn ($state): string => is_array($state) && $state !== [] ? implode(', ', $state) : '-')
                    ->placeholder('-'),
                ImageEntry::make('home_preview_url')
                    ->label('Vista previa del home')
                    ->disk('public')
                    ->height(220)
                    ->extraImgAttributes(['class' => 'rounded-xl border border-white/10'])
                    ->placeholder('-'),
                TextEntry::make('updated_at')->label('Actualizado')->dateTime()->placeholder('-'),
            ]);
    }
}
