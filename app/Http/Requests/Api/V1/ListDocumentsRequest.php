<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ListDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $catalogFilters = $this->user() !== null;

        return [
            'system_id' => $catalogFilters ? ['sometimes', 'integer', 'exists:systems,id'] : ['prohibited'],
            'module_id' => $catalogFilters ? ['nullable', 'integer', 'exists:modules,id'] : ['prohibited'],
            'endpoint_id' => $catalogFilters ? ['nullable', 'integer', 'exists:endpoints,id'] : ['prohibited'],
            'q' => ['nullable', 'string', 'max:255'],
        ];
    }
}
