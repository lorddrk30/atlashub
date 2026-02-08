<?php

namespace App\Domain\Reports\DTOs;

use Illuminate\Http\Request;

class ReportPdfOptionsData
{
    public function __construct(
        public readonly string $title,
        public readonly string $theme,
        public readonly string $disposition,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            title: trim((string) $request->input('title', 'Reporte AtlasHub')) ?: 'Reporte AtlasHub',
            theme: (string) $request->input('theme', 'dark'),
            disposition: (string) $request->input('disposition', 'download'),
        );
    }
}

