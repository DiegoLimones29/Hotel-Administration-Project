<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Repositories\ReportRepository;
use App\Http\Requests\ReportRequests\ReportFiltersRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    protected $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    public function occupancy(ReportFiltersRequest $request)
    {
        $data = $request->validated();
        $result = $this->reportRepository->occupancyReport($data['start_date'], $data['end_date'], $data['room_type_id'] ?? null);
        return response()->json($result, isset($result['data']) ? 200 : 422);
    }

    public function revenue(ReportFiltersRequest $request)
    {
        $data = $request->validated();
        $result = $this->reportRepository->revenueReport($data['start_date'], $data['end_date'], $data['room_type_id'] ?? null);
        return response()->json($result, isset($result['data']) ? 200 : 422);
    }

    public function mostRequestedRoomTypes(ReportFiltersRequest $request)
    {
        $data = $request->validated();
        $result = $this->reportRepository->mostRequestedRoomTypes($data['start_date'], $data['end_date']);
        return response()->json($result, isset($result['data']) ? 200 : 422);
    }

    public function frequentGuests(ReportFiltersRequest $request)
    {
        $data = $request->validated();
        $result = $this->reportRepository->frequentGuests($data['start_date'], $data['end_date']);
        return response()->json($result, isset($result['data']) ? 200 : 422);
    }

    public function dailySummary(Request $request)
    {
        $result = $this->reportRepository->dailySummary($request->query('date'));
        return response()->json($result, isset($result['data']) ? 200 : 422);
    }

    /**
     * Exportación en CSV (se abre directamente en Excel), sin dependencias
     * adicionales. type: ocupacion | ingresos | habitaciones | huespedes
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $type = $request->query('type');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $roomTypeId = $request->query('room_type_id');

        [$filename, $headers, $rows] = $this->buildExportData($type, $startDate, $endDate, $roomTypeId);

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Exportación en PDF. Requiere: composer require barryvdh/laravel-dompdf
     */
    public function exportPdf(Request $request)
    {
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return response()->json([
                'message' => 'La exportación a PDF requiere el paquete barryvdh/laravel-dompdf. Corre: composer require barryvdh/laravel-dompdf'
            ], 501);
        }

        $type = $request->query('type');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $roomTypeId = $request->query('room_type_id');

        [$filename, $headers, $rows, $title] = $this->buildExportData($type, $startDate, $endDate, $roomTypeId, true);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf', [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

        return $pdf->download(str_replace('.csv', '.pdf', $filename));
    }

    private function buildExportData(?string $type, ?string $startDate, ?string $endDate, $roomTypeId = null, bool $withTitle = false)
    {
        $roomTypeId = $roomTypeId ? (int) $roomTypeId : null;

        switch ($type) {
            case 'ocupacion':
                $result = $this->reportRepository->occupancyReport($startDate, $endDate, $roomTypeId);
                $headers = ['Fecha', 'Habitaciones ocupadas', 'Total habitaciones', 'Ocupación %'];
                $rows = collect($result['data']['days'] ?? [])->map(fn ($d) => [
                    $d['date'], $d['occupied'], $d['total_rooms'], $d['occupancy_percent']
                ])->toArray();
                return ["reporte-ocupacion.csv", $headers, $rows, "Reporte de Ocupación"];

            case 'ingresos':
                $result = $this->reportRepository->revenueReport($startDate, $endDate, $roomTypeId);
                $headers = ['Factura', 'Reservación', 'Huésped', 'Habitación', 'Costo Habitación', 'Costo Servicios', 'Total', 'Fecha'];
                $rows = collect($result['data']['invoices'] ?? [])->map(fn ($inv) => [
                    $inv->id,
                    $inv->reservation_id,
                    $inv->reservation?->guest?->name ?? '',
                    $inv->reservation?->room?->room_number ?? '',
                    $inv->room_cost,
                    $inv->services_cost,
                    $inv->total_cost,
                    optional($inv->issued_at)->format('Y-m-d'),
                ])->toArray();
                return ["reporte-ingresos.csv", $headers, $rows, "Reporte de Ingresos"];

            case 'habitaciones':
                $result = $this->reportRepository->mostRequestedRoomTypes($startDate, $endDate);
                $headers = ['Tipo de habitación', 'Total de reservaciones'];
                $rows = collect($result['data'] ?? [])->map(fn ($r) => [$r->type, $r->total_reservations])->toArray();
                return ["reporte-habitaciones-mas-solicitadas.csv", $headers, $rows, "Habitaciones Más Solicitadas"];

            case 'huespedes':
                $result = $this->reportRepository->frequentGuests($startDate, $endDate);
                $headers = ['Huésped', 'Correo', 'Total de estadías'];
                $rows = collect($result['data'] ?? [])->map(fn ($r) => [$r['name'], $r['email'], $r['total_stays']])->toArray();
                return ["reporte-huespedes-frecuentes.csv", $headers, $rows, "Huéspedes Frecuentes"];

            default:
                return ["reporte.csv", ['Error'], [['Tipo de reporte no reconocido']], "Reporte"];
        }
    }
}
