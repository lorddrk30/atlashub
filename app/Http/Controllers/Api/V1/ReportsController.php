<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Reports\DTOs\ReportFiltersData;
use App\Domain\Reports\DTOs\ReportPdfOptionsData;
use App\Domain\Reports\Services\ReportService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\GenerateReportPdfRequest;
use App\Http\Requests\Api\V1\ReportSummaryRequest;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportsController extends Controller
{
    public function summary(ReportSummaryRequest $request, ReportService $service)
    {
        $filters = ReportFiltersData::fromRequest($request);
        $summary = $service->summary($filters);

        return response()->json($summary);
    }

    public function generatePdf(GenerateReportPdfRequest $request, ReportService $service)
    {
        try {
            $filters = ReportFiltersData::fromRequest($request);
            $options = ReportPdfOptionsData::fromRequest($request);
            $pdf = $service->buildPdf($filters, $options);
            $filename = 'atlashub-reporte-'.now()->format('Ymd-His').'.pdf';

            if ($options->disposition === 'inline') {
                return response($pdf->output(), 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="'.$filename.'"',
                ]);
            }

            return $pdf->download($filename);
        } catch (Throwable $exception) {
            Log::error('No fue posible generar el reporte PDF.', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'No se pudo generar el PDF en este momento.',
                'code' => 'PDF_GENERATION_ERROR',
                'action' => 'retry_later',
            ], 500);
        }
    }
}
