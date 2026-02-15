<?php

namespace App\Domain\Reports\DTOs;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ReportFiltersData
{
    public function __construct(
        public readonly ?int $systemId,
        public readonly ?int $moduleId,
        public readonly ?string $systemStatus,
        public readonly ?string $status,
        public readonly ?CarbonImmutable $dateFrom,
        public readonly ?CarbonImmutable $dateTo,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            systemId: $request->filled('system_id') ? (int) $request->input('system_id') : null,
            moduleId: $request->filled('module_id') ? (int) $request->input('module_id') : null,
            systemStatus: $request->filled('system_status') ? (string) $request->input('system_status') : null,
            status: $request->filled('status') ? (string) $request->input('status') : null,
            dateFrom: $request->filled('date_from')
                ? CarbonImmutable::parse((string) $request->input('date_from'))->startOfDay()
                : null,
            dateTo: $request->filled('date_to')
                ? CarbonImmutable::parse((string) $request->input('date_to'))->endOfDay()
                : null,
        );
    }

    public function resolvedEndpointStatuses(): ?array
    {
        if (! $this->status) {
            return null;
        }

        return match ($this->status) {
            'active' => ['published'],
            'deprecated' => ['draft', 'deprecated'],
            'archived' => ['archived'],
            default => null,
        };
    }

    public function toArray(): array
    {
        return [
            'system_id' => $this->systemId,
            'module_id' => $this->moduleId,
            'system_status' => $this->systemStatus,
            'status' => $this->status,
            'date_from' => $this->dateFrom?->toDateString(),
            'date_to' => $this->dateTo?->toDateString(),
        ];
    }
}
