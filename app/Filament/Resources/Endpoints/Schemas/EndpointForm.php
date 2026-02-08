<?php

namespace App\Filament\Resources\Endpoints\Schemas;

use App\Models\Endpoint;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EndpointForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('module_id')
                    ->label('Modulo')
                    ->relationship('module', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Nombre del endpoint')
                    ->required()
                    ->maxLength(255),
                Select::make('method')
                    ->label('Metodo HTTP')
                    ->options(array_combine(Endpoint::METHODS, Endpoint::METHODS))
                    ->required(),
                TextInput::make('path')
                    ->label('Ruta')
                    ->required()
                    ->maxLength(255),
                Select::make('authentication_type')
                    ->label('Tipo de autenticacion')
                    ->options(array_combine(Endpoint::AUTH_TYPES, Endpoint::AUTH_TYPES))
                    ->default('none')
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->options(array_combine(Endpoint::STATUSES, Endpoint::STATUSES))
                    ->default('published')
                    ->disabled(fn (): bool => ! (auth()->user()?->can('endpoint.publish') ?? false))
                    ->required(),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->rows(4)
                    ->columnSpanFull(),
                Repeater::make('parameters')
                    ->label('Parametros')
                    ->schema([
                        TextInput::make('name')->label('Nombre')->required(),
                        Select::make('in')
                            ->label('Ubicacion')
                            ->options([
                                'path' => 'path',
                                'query' => 'query',
                                'body' => 'body',
                                'header' => 'header',
                            ])
                            ->required(),
                        TextInput::make('type')->label('Tipo')->required(),
                        Toggle::make('required')
                            ->label('Requerido')
                            ->default(true),
                        Textarea::make('description')->label('Descripcion')->rows(2),
                    ])->columnSpanFull(),
                KeyValue::make('request_example')
                    ->label('Ejemplo request')
                    ->keyLabel('Campo')
                    ->valueLabel('Valor')
                    ->columnSpanFull(),
                KeyValue::make('response_example')
                    ->label('Ejemplo response')
                    ->keyLabel('Campo')
                    ->valueLabel('Valor')
                    ->columnSpanFull(),
                KeyValue::make('urls')
                    ->label('URLs por ambiente')
                    ->keyLabel('Ambiente')
                    ->valueLabel('URL')
                    ->columnSpanFull(),
            ]);
    }
}
