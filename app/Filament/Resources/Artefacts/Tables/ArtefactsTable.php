<?php

namespace App\Filament\Resources\Artefacts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArtefactsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Titulo')->searchable()->sortable(),
                BadgeColumn::make('type')->label('Tipo'),
                TextColumn::make('url')->label('URL')->limit(40)->searchable(),
                TextColumn::make('system.name')->label('Sistema')->toggleable(),
                TextColumn::make('module.name')->label('Modulo')->toggleable(),
                TextColumn::make('endpoint.name')->label('Endpoint')->toggleable(),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipo')
                    ->options([
                        'swagger' => 'swagger',
                        'postman' => 'postman',
                        'repo' => 'repo',
                        'docs' => 'docs',
                        'runbook' => 'runbook',
                        'dashboard' => 'dashboard',
                        'other' => 'other',
                    ]),
                SelectFilter::make('system_id')
                    ->label('Sistema')
                    ->relationship('system', 'name'),
                SelectFilter::make('module_id')
                    ->label('Modulo')
                    ->relationship('module', 'name'),
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
