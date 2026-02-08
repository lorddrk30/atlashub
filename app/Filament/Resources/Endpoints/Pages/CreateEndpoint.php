<?php

namespace App\Filament\Resources\Endpoints\Pages;

use App\Filament\Resources\Endpoints\EndpointResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEndpoint extends CreateRecord
{
    protected static string $resource = EndpointResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->can('endpoint.publish')) {
            $data['status'] = 'draft';
        }

        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
