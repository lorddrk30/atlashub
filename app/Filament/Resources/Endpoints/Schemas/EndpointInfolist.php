<?php

namespace App\Filament\Resources\Endpoints\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EndpointInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')->label('Nombre'),
                TextEntry::make('method')->label('Metodo')->badge(),
                TextEntry::make('path')->label('Ruta'),
                TextEntry::make('description')->label('Descripcion')->markdown()->placeholder('-'),
                TextEntry::make('module.system.name')->label('Sistema')->placeholder('-'),
                TextEntry::make('module.name')->label('Modulo')->placeholder('-'),
                TextEntry::make('authentication_type')->label('Autenticacion')->badge(),
                TextEntry::make('status')->label('Estado')->badge(),
                TextEntry::make('parameters')
                    ->label('Parametros')
                    ->formatStateUsing(fn ($state) => static::formatJsonState($state))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('request_example')
                    ->label('Ejemplo request')
                    ->formatStateUsing(fn ($state) => static::formatJsonState($state))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('response_example')
                    ->label('Ejemplo response')
                    ->formatStateUsing(fn ($state) => static::formatJsonState($state))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('urls')
                    ->label('URLs por ambiente')
                    ->formatStateUsing(fn ($state) => static::formatJsonState($state))
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('updated_at')->label('Actualizado')->dateTime()->placeholder('-'),
            ]);
    }

    private static function formatJsonState(mixed $state): string
    {
        if ($state === null || $state === '') {
            return '-';
        }

        if (is_array($state)) {
            return (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $state;
    }
}
