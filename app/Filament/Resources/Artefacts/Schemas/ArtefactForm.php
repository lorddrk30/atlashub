<?php

namespace App\Filament\Resources\Artefacts\Schemas;

use App\Models\Artefact;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ArtefactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('system_id')
                    ->label('Sistema')
                    ->relationship('system', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('module_id')
                    ->label('Modulo')
                    ->relationship('module', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('endpoint_id')
                    ->label('Endpoint')
                    ->relationship('endpoint', 'name')
                    ->searchable()
                    ->preload(),
                Select::make('type')
                    ->label('Tipo')
                    ->options(array_combine(Artefact::TYPES, Artefact::TYPES))
                    ->required(),
                TextInput::make('title')
                    ->label('Titulo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('URL')
                    ->url()
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                KeyValue::make('metadata')
                    ->label('Metadata')
                    ->columnSpanFull(),
            ]);
    }
}
