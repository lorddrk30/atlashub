<?php

namespace App\Filament\Resources\OrganizationSettings\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrganizationSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la organizacion')
                    ->required()
                    ->maxLength(255),
                TextInput::make('short_name')
                    ->label('Nombre corto')
                    ->required()
                    ->maxLength(16),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('tagline')
                    ->label('Tagline')
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('logo_url')
                    ->label('URL de logo')
                    ->url()
                    ->maxLength(255),
                TextInput::make('favicon_url')
                    ->label('URL de favicon')
                    ->url()
                    ->maxLength(255),
                TextInput::make('support_email')
                    ->label('Correo de soporte')
                    ->email()
                    ->maxLength(255),
                TextInput::make('primary_color')
                    ->label('Color primario')
                    ->required()
                    ->regex('/^#[0-9A-Fa-f]{6}$/')
                    ->helperText('Formato hex. Ejemplo: #22d3ee'),
                TextInput::make('secondary_color')
                    ->label('Color secundario')
                    ->required()
                    ->regex('/^#[0-9A-Fa-f]{6}$/')
                    ->helperText('Formato hex. Ejemplo: #34d399'),
            ]);
    }
}

