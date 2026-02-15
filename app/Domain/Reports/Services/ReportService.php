<?php

namespace App\Domain\Reports\Services;

use App\Domain\Reports\DTOs\ReportFiltersData;
use App\Domain\Reports\DTOs\ReportPdfOptionsData;
use App\Domain\Reports\Repositories\ReportRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use Illuminate\Support\Str;

class ReportService
{
    public function __construct(private readonly ReportRepository $repository)
    {
    }

    public function summary(ReportFiltersData $filters): array
    {
        $payload = $this->repository->summary($filters);

        return [
            ...$payload,
            'filters_applied' => $filters->toArray(),
            'executive_summary' => $this->executiveSummary($payload['kpis']),
            'generated_at' => now()->toIso8601String(),
        ];
    }

    public function buildPdf(ReportFiltersData $filters, ReportPdfOptionsData $options): DomPdfWrapper
    {
        $summary = $this->summary($filters);
        $theme = in_array($options->theme, ['dark', 'light'], true) ? $options->theme : 'dark';

        $chartImages = [
            'systems_by_status' => $this->buildBarChartDataUri($summary['charts']['systems_by_status'], 'Sistemas por estado', $theme),
            'endpoints_by_system' => $this->buildBarChartDataUri($summary['charts']['endpoints_by_system'], 'Endpoints por sistema', $theme),
            'endpoints_by_module' => $this->buildBarChartDataUri($summary['charts']['endpoints_by_module'], 'Endpoints por modulo', $theme),
            'artefacts_by_type' => $this->buildBarChartDataUri($summary['charts']['artefacts_by_type'], 'Artefactos por tipo', $theme),
            'endpoints_by_method' => $this->buildBarChartDataUri($summary['charts']['endpoints_by_method'], 'Endpoints por metodo HTTP', $theme),
        ];

        return Pdf::loadView('reports.summary-pdf', [
            'title' => $options->title,
            'theme' => $theme,
            'generatedAt' => now()->format('d/m/Y H:i'),
            'summary' => $summary,
            'chartImages' => $chartImages,
            'logoDataUri' => $this->atlasHubLogoDataUri($theme),
        ])->setPaper('a4');
    }

    private function executiveSummary(array $kpis): string
    {
        $systems = (int) ($kpis['systems'] ?? 0);
        $modules = (int) ($kpis['modules'] ?? 0);
        $endpoints = (int) ($kpis['endpoints'] ?? 0);
        $artefacts = (int) ($kpis['artefacts'] ?? 0);

        return "AtlasHub registra {$systems} sistemas (publicados, en borrador y descartados) con {$modules} modulos, {$endpoints} endpoints documentados y {$artefacts} artefactos tecnicos.";
    }

    private function buildBarChartDataUri(array $series, string $title, string $theme): string
    {
        $width = 1000;
        $height = 430;
        $paddingX = 70;
        $paddingTop = 76;
        $paddingBottom = 90;
        $plotWidth = $width - ($paddingX * 2);
        $plotHeight = $height - $paddingTop - $paddingBottom;

        $values = collect($series)->pluck('value')->map(fn ($value): int => (int) $value);
        $maxValue = max(1, (int) $values->max());
        $count = max(1, count($series));
        $barGap = 16;
        $barWidth = max(14, (int) floor(($plotWidth - (($count - 1) * $barGap)) / $count));

        $bg = $theme === 'light' ? '#f8fafc' : '#071025';
        $text = $theme === 'light' ? '#0f172a' : '#f8fafc';
        $textSoft = $theme === 'light' ? '#334155' : '#cbd5e1';
        $bar = $theme === 'light' ? '#0ea5e9' : '#22d3ee';
        $grid = $theme === 'light' ? '#cbd5e1' : '#334155';
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $axisBottomY = $paddingTop + $plotHeight;
        $axisRightX = $paddingX + $plotWidth;

        $barsSvg = '';
        foreach ($series as $index => $item) {
            $value = (int) ($item['value'] ?? 0);
            $label = Str::limit((string) ($item['label'] ?? 'N/A'), 20, '...');
            $x = $paddingX + (($barWidth + $barGap) * $index);
            $barHeight = (int) floor(($value / $maxValue) * $plotHeight);
            $y = $paddingTop + ($plotHeight - $barHeight);
            $valueY = $y - 8;
            $labelY = $paddingTop + $plotHeight + 20;

            $barsSvg .= '<rect x="'.$x.'" y="'.$y.'" width="'.$barWidth.'" height="'.$barHeight.'" rx="8" fill="'.$bar.'" />';
            $barsSvg .= '<text x="'.($x + ($barWidth / 2)).'" y="'.$valueY.'" text-anchor="middle" fill="'.$text.'" font-size="14" font-weight="700">'.$value.'</text>';
            $barsSvg .= '<text x="'.($x + ($barWidth / 2)).'" y="'.$labelY.'" text-anchor="middle" fill="'.$textSoft.'" font-size="12">'.htmlspecialchars($label, ENT_QUOTES, 'UTF-8').'</text>';
        }

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}" viewBox="0 0 {$width} {$height}">
  <rect x="0" y="0" width="{$width}" height="{$height}" rx="20" fill="{$bg}" />
  <text x="{$paddingX}" y="42" fill="{$text}" font-size="22" font-weight="700">{$safeTitle}</text>
  <line x1="{$paddingX}" y1="{$paddingTop}" x2="{$paddingX}" y2="{$axisBottomY}" stroke="{$grid}" stroke-width="1" />
  <line x1="{$paddingX}" y1="{$axisBottomY}" x2="{$axisRightX}" y2="{$axisBottomY}" stroke="{$grid}" stroke-width="1" />
  {$barsSvg}
</svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    private function atlasHubLogoDataUri(string $theme): string
    {
        $bg = $theme === 'light' ? '#0f172a' : '#22d3ee';
        $text = $theme === 'light' ? '#e2e8f0' : '#032126';

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="140" height="140" viewBox="0 0 140 140">
  <rect x="8" y="8" width="124" height="124" rx="30" fill="{$bg}" />
  <text x="70" y="82" text-anchor="middle" fill="{$text}" font-size="48" font-weight="700" font-family="Arial, sans-serif">AH</text>
</svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
