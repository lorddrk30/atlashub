<?php

namespace App\Filament\Resources\Systems\Pages;

use App\Filament\Resources\Systems\SystemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSystem extends CreateRecord
{
    protected static string $resource = SystemResource::class;

    protected static bool $canCreateAnother = false;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Sistema creado. Ahora puedes subir manuales en la pestana Documentos.';
    }
}

