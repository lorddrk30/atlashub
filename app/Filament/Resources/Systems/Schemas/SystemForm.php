<?php

namespace App\Filament\Resources\Systems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SystemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('prod_server')
                    ->label('Servidor PROD')
                    ->placeholder('prod-api-01.rikarcoffe.local')
                    ->maxLength(255),
                TextInput::make('uat_server')
                    ->label('Servidor UAT')
                    ->placeholder('uat-api-01.rikarcoffe.local')
                    ->maxLength(255),
                TextInput::make('dev_server')
                    ->label('Servidor DEV')
                    ->placeholder('dev-api-01.rikarcoffe.local')
                    ->maxLength(255),
                TextInput::make('internal_url')
                    ->label('Dominio interno')
                    ->placeholder('https://sistema.rikarcoffe.local')
                    ->url()
                    ->maxLength(255),
                TextInput::make('public_url')
                    ->label('Dominio publico')
                    ->placeholder('https://app.rikarcoffe.com')
                    ->url()
                    ->maxLength(255),
                TagsInput::make('responsibles')
                    ->label('Responsables del sistema')
                    ->placeholder('Agrega un responsable y presiona Enter')
                    ->columnSpanFull(),
                TagsInput::make('user_areas')
                    ->label('Areas usuarias')
                    ->placeholder('Agrega un area usuaria y presiona Enter')
                    ->columnSpanFull(),
                TextInput::make('repository_url')
                    ->label('URL del repositorio')
                    ->placeholder('https://github.com/org/proyecto')
                    ->helperText('Compatible con GitHub, GitLab, Bitbucket u otro repositorio.')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
                FileUpload::make('home_preview_url')
                    ->label('Captura del home')
                    ->helperText('Sube una captura de la pantalla principal del sistema.')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('systems/previews')
                    ->visibility('public')
                    ->maxSize(5120)
                    ->columnSpanFull(),
            ]);
    }
}
