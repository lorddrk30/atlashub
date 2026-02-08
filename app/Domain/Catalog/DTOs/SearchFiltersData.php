<?php

namespace App\Domain\Catalog\DTOs;

use Illuminate\Http\Request;

class SearchFiltersData
{
    public function __construct(
        public readonly ?string $query,
        public readonly ?int $systemId,
        public readonly ?int $moduleId,
        public readonly ?string $method,
        public readonly ?string $authenticationType,
        public readonly ?string $artefactType,
        public readonly int $perCategory,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            query: trim((string) $request->query('q', '')) ?: null,
            systemId: $request->query('system_id') ? (int) $request->query('system_id') : null,
            moduleId: $request->query('module_id') ? (int) $request->query('module_id') : null,
            method: $request->query('method') ?: null,
            authenticationType: $request->query('authentication_type') ?: null,
            artefactType: $request->query('artefact_type') ?: null,
            perCategory: max(1, min((int) $request->query('per_category', 12), 50)),
        );
    }
}
