<?php

namespace App\Filament\Resources\Systems\Pages;

use App\Filament\Resources\Systems\SystemResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditSystem extends EditRecord
{
    protected static string $resource = SystemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            Action::make('discard')
                ->label('Descartar')
                ->color('gray')
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Descartar sistema')
                ->modalDescription('El sistema quedara como descartado y no aparecera en el catalogo.')
                ->visible(fn (): bool => $this->record->status !== 'discarded')
                ->action(function (): void {
                    $this->record->update(['status' => 'discarded']);

                    Notification::make()
                        ->title('Sistema descartado.')
                        ->success()
                        ->send();

                    $this->redirect(static::getResource()::getUrl('index'));
                }),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->status === 'draft') {
            $data['status'] = 'published';
        }

        if ($this->record->status === 'discarded') {
            $data['status'] = 'discarded';
        }

        return $data;
    }
}
