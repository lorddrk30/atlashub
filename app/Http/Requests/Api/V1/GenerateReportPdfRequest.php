<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GenerateReportPdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'system_id' => ['nullable', 'integer', 'exists:systems,id'],
            'module_id' => ['nullable', 'integer', 'exists:modules,id'],
            'system_status' => ['nullable', 'string', Rule::in(['draft', 'published', 'discarded'])],
            'status' => ['nullable', 'string', Rule::in(['active', 'deprecated', 'archived'])],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
            'title' => ['nullable', 'string', 'max:120'],
            'theme' => ['nullable', 'string', Rule::in(['dark', 'light'])],
            'disposition' => ['nullable', 'string', Rule::in(['inline', 'download'])],
        ];
    }
}
