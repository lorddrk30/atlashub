<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Artefact;
use App\Models\Endpoint;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'system_id' => ['nullable', 'integer', 'exists:systems,id'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'method' => ['nullable', 'string', Rule::in(Endpoint::METHODS)],
            'authentication_type' => ['nullable', 'string', Rule::in(Endpoint::AUTH_TYPES)],
            'artefact_type' => ['nullable', 'string', Rule::in(Artefact::TYPES)],
            'per_category' => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
