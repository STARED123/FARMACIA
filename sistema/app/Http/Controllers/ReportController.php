<?php

namespace App\Http\Controllers;

use App\Exports\ReportsExport;
use App\Models\Medicine;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeReportAccess($request);

        return view('reports.index', $this->reportData($request));
    }

    public function pdf(Request $request): Response
    {
        $this->authorizeReportAccess($request);

        return Pdf::loadView('reports.pdf', $this->reportData($request))
            ->setPaper('a4', 'landscape')
            ->download('reporte-farmacia.pdf');
    }

    public function excel(Request $request): Response
    {
        $this->authorizeReportAccess($request);
        $reportData = $this->reportData($request);

        return Excel::download(
            new ReportsExport($reportData['sales'], $reportData['inventory']),
            'reporte-farmacia.xlsx',
        );
    }

    private function authorizeReportAccess(Request $request): void
    {
        abort_unless(
            $request->user()?->hasRole('Administrador', 'Químico farmacéutico'),
            Response::HTTP_FORBIDDEN,
        );
    }

    /**
     * @return array{
     *     sales: EloquentCollection<int, Sale>,
     *     inventory: EloquentCollection<int, Medicine>,
     *     startDate: CarbonImmutable|null,
     *     endDate: CarbonImmutable|null,
     *     salesTotal: float
     * }
     */
    private function reportData(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'required_with:end_date', 'date'],
            'end_date' => ['nullable', 'required_with:start_date', 'date', 'after_or_equal:start_date'],
        ], [
            'start_date.required_with' => 'Indica ambas fechas para aplicar el filtro.',
            'end_date.required_with' => 'Indica ambas fechas para aplicar el filtro.',
            'end_date.after_or_equal' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
        ]);

        $startDate = isset($validated['start_date'])
            ? CarbonImmutable::parse($validated['start_date'])
            : null;
        $endDate = isset($validated['end_date'])
            ? CarbonImmutable::parse($validated['end_date'])
            : null;

        $salesQuery = Sale::query()
            ->select(['id', 'customer_id', 'users_id', 'date', 'total', 'igv'])
            ->with(['customer:id,name', 'user:id,name']);

        if ($startDate !== null && $endDate !== null) {
            $salesQuery->whereBetween('date', [
                $startDate->startOfDay(),
                $endDate->endOfDay(),
            ]);
        }

        $sales = $salesQuery->latest('date')->get();
        $inventory = Medicine::query()
            ->select(['id', 'category_id', 'amount', 'price_unit', 'subtotal'])
            ->with('category:id,name')
            ->orderBy('id')
            ->get();

        return [
            'sales' => $sales,
            'inventory' => $inventory,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'salesTotal' => (float) $sales->sum('total'),
        ];
    }
}
