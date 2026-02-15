<?php

namespace App\Filament\Resources\Systems\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SystemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->live(debounce: 1200)
                    ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                        if (filled($get('slug'))) {
                            return;
                        }

                        $set('slug', Str::slug((string) $state));
                    })
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->live(debounce: 1200)
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Textarea::make('description')
                    ->label('Descripcion')
                    ->live(debounce: 1200)
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('prod_server')
                    ->label('Servidor PROD')
                    ->placeholder('prod-api-01.rikarcoffe.local')
                    ->live(debounce: 1200)
                    ->maxLength(255),
                TextInput::make('prod_server_ip')
                    ->label('IP PROD')
                    ->placeholder('10.0.10.21')
                    ->ip()
                    ->live(debounce: 1200)
                    ->maxLength(45),
                TextInput::make('uat_server')
                    ->label('Servidor UAT')
                    ->placeholder('uat-api-01.rikarcoffe.local')
                    ->live(debounce: 1200)
                    ->maxLength(255),
                TextInput::make('uat_server_ip')
                    ->label('IP UAT')
                    ->placeholder('10.0.20.21')
                    ->ip()
                    ->live(debounce: 1200)
                    ->maxLength(45),
                TextInput::make('dev_server')
                    ->label('Servidor DEV')
                    ->placeholder('dev-api-01.rikarcoffe.local')
                    ->live(debounce: 1200)
                    ->maxLength(255),
                TextInput::make('dev_server_ip')
                    ->label('IP DEV')
                    ->placeholder('10.0.30.21')
                    ->ip()
                    ->live(debounce: 1200)
                    ->maxLength(45),
                TextInput::make('internal_url')
                    ->label('Dominio interno')
                    ->placeholder('https://sistema.rikarcoffe.local')
                    ->url()
                    ->live(debounce: 1200)
                    ->maxLength(255),
                TextInput::make('public_url')
                    ->label('Dominio publico')
                    ->placeholder('https://app.rikarcoffe.com')
                    ->url()
                    ->live(debounce: 1200)
                    ->maxLength(255),
                TagsInput::make('responsibles')
                    ->label('Responsables del sistema')
                    ->placeholder('Agrega un responsable y presiona Enter')
                    ->live(debounce: 1200)
                    ->columnSpanFull(),
                TagsInput::make('user_areas')
                    ->label('Areas usuarias')
                    ->placeholder('Agrega un area usuaria y presiona Enter')
                    ->live(debounce: 1200)
                    ->columnSpanFull(),
                TextInput::make('repository_url')
                    ->label('URL del repositorio')
                    ->placeholder('https://github.com/org/proyecto')
                    ->helperText('Compatible con GitHub, GitLab, Bitbucket u otro repositorio.')
                    ->url()
                    ->live(debounce: 1200)
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
