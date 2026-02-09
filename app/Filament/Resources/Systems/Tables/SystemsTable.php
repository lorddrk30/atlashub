<?php

namespace App\Filament\Resources\Systems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SystemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                ImageColumn::make('home_preview_url')
                    ->label('Preview')
                    ->disk('public')
                    ->square()
                    ->size(44)
                    ->toggleable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('description')->label('Descripcion')->limit(60),
                TextColumn::make('prod_server')->label('PROD')->toggleable()->searchable()->placeholder('-'),
                TextColumn::make('internal_url')
                    ->label('Dominio interno')
                    ->limit(32)
                    ->url(fn ($record) => $record->internal_url, true)
                    ->toggleable(),
                TextColumn::make('repository_url')
                    ->label('Repositorio')
                    ->state(fn ($record) => $record->repository_url ?: $record->gitlab_url)
                    ->formatStateUsing(fn ($state) => filled($state) ? 'Abrir repo' : '-')
                    ->url(fn ($record) => $record->repository_url ?: $record->gitlab_url, shouldOpenInNewTab: true)
                    ->toggleable(),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime()->sortable(),
            ])
            ->filters([])
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
