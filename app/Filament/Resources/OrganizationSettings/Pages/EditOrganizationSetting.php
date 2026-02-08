<?php

namespace App\Filament\Resources\OrganizationSettings\Pages;

use App\Filament\Resources\OrganizationSettings\OrganizationSettingResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationSetting extends EditRecord
{
    protected static string $resource = OrganizationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}

