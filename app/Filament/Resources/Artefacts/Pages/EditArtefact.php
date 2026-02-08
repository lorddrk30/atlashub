<?php

namespace App\Filament\Resources\Artefacts\Pages;

use App\Filament\Resources\Artefacts\ArtefactResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditArtefact extends EditRecord
{
    protected static string $resource = ArtefactResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = auth()->id();

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
