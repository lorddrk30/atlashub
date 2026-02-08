<?php

namespace App\Filament\Resources\OrganizationSettings\Pages;

use App\Filament\Resources\OrganizationSettings\OrganizationSettingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganizationSetting extends ViewRecord
{
    protected static string $resource = OrganizationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

