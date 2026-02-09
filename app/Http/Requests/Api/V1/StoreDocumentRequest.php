<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Document;
use App\Models\Endpoint;
use App\Models\Module;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDocumentRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(Document::TYPES)],
            'file' => ['required', 'file', 'mimetypes:application/pdf', 'max:20480'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $systemId = (int) $this->input('system_id');
            $moduleId = $this->input('module_id');
            $endpointId = $this->input('endpoint_id');

            if ($moduleId) {
                $module = Module::query()->find((int) $moduleId);

                if ($module && $module->system_id !== $systemId) {
                    $validator->errors()->add('module_id', 'El modulo no pertenece al sistema seleccionado.');
                }
            }

            if ($endpointId) {
                $endpoint = Endpoint::query()->with('module:id,system_id')->find((int) $endpointId);

                if ($endpoint && $endpoint->module?->system_id !== $systemId) {
                    $validator->errors()->add('endpoint_id', 'El endpoint no pertenece al sistema seleccionado.');
                }

                if ($endpoint && $moduleId && $endpoint->module_id !== (int) $moduleId) {
                    $validator->errors()->add('endpoint_id', 'El endpoint no pertenece al modulo seleccionado.');
                }
            }
        });
    }
}
