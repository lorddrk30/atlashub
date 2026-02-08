<?php

namespace App\Filament\Resources\Artefacts\Pages;

use App\Filament\Resources\Artefacts\ArtefactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArtefact extends CreateRecord
{
    protected static string $resource = ArtefactResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
