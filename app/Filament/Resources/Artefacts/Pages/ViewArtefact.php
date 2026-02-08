<?php

namespace App\Filament\Resources\Artefacts\Pages;

use App\Filament\Resources\Artefacts\ArtefactResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewArtefact extends ViewRecord
{
    protected static string $resource = ArtefactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
