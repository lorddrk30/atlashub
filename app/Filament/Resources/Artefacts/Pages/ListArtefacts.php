<?php

namespace App\Filament\Resources\Artefacts\Pages;

use App\Filament\Resources\Artefacts\ArtefactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArtefacts extends ListRecords
{
    protected static string $resource = ArtefactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
