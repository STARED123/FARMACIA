<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Filtro de fechas (por defecto: mes actual)
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->toDateString());

        // 1. Ventas en el rango seleccionado
        $ventas = Sale::with(['user', 'customer'])
            ->whereBetween(DB::raw('DATE(created_at)'), [$fechaInicio, $fechaFin])
            ->orderByDesc('created_at')
            ->get();

        // 2. KPIs principales
        $totalIngresos = $ventas->sum('total');
        $cantidadVentas = $ventas->count();
        $ticketPromedio = $cantidadVentas > 0 ? $totalIngresos / $cantidadVentas : 0;

        // 3. Top 5 medicamentos más vendidos en el periodo
        $topMedicamentos = DB::table('details')
            ->join('sales', 'details.sale_id', '=', 'sales.id')
            ->join('medicines', 'details.medicine_id', '=', 'medicines.id')
            ->whereBetween(DB::raw('DATE(sales.created_at)'), [$fechaInicio, $fechaFin])
            ->select(
                'medicines.name',
                DB::raw('SUM(details.quantity) as total_vendido'),
                DB::raw('SUM(details.subtotal) as total_recaudado')
            )
            ->groupBy('medicines.id', 'medicines.name')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        // 4. Medicamentos con stock bajo (<= 10 unidades)
        $stockBajo = Medicine::where('stock', '<=', 10)
            ->orderBy('stock')
            ->get();

        return view('reports.index', compact(
            'fechaInicio',
            'fechaFin',
            'ventas',
            'totalIngresos',
            'cantidadVentas',
            'ticketPromedio',
            'topMedicamentos',
            'stockBajo'
        ));
    }
}