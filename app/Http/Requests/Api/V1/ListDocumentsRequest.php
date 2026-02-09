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
        return [
            'system_id' => ['required', 'integer', 'exists:systems,id'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'endpoint_id' => ['nullable', 'integer', 'exists:endpoints,id'],
            'q' => ['nullable', 'string', 'max:255'],
        ];
    }
}
