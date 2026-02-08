<?php

namespace App\Filament\Resources\OrganizationSettings\Pages;

use App\Filament\Resources\OrganizationSettings\OrganizationSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationSettings extends ListRecords
{
    protected static string $resource = OrganizationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

