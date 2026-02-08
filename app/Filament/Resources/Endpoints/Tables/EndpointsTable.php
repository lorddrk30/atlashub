<?php

namespace App\Filament\Resources\Endpoints\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EndpointsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                BadgeColumn::make('method')->label('Metodo')->searchable(),
                TextColumn::make('path')->label('Ruta')->searchable(),
                TextColumn::make('module.system.name')->label('Sistema')->searchable(),
                TextColumn::make('module.name')->label('Modulo')->searchable(),
                BadgeColumn::make('authentication_type')->label('Auth'),
                BadgeColumn::make('status')->label('Estado'),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('module_id')
                    ->label('Modulo')
                    ->relationship('module', 'name'),
                SelectFilter::make('method')
                    ->label('Metodo')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
                    ]),
                SelectFilter::make('authentication_type')
                    ->label('Autenticacion')
                    ->options([
                        'none' => 'none',
                        'bearer' => 'bearer',
                        'basic' => 'basic',
                        'api_key' => 'api_key',
                        'oauth2' => 'oauth2',
                        'session' => 'session',
                        'custom' => 'custom',
                    ]),
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'draft' => 'draft',
                        'published' => 'published',
                        'archived' => 'archived',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
