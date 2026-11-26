<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use LogicException;

class DashboardController extends Controller
{
    public function index()
    {
        $salesToday = Sale::whereDate('date', today())->sum('total');
        $salesMonth = Sale::whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('total');

        $customersCount = Customer::count();
        $medicinesCount = Medicine::count();
        $lowStockCount = Medicine::where('amount', '<=', 10)->count();

        $lowStockMedicines = Medicine::with('category')
            ->where('amount', '<=', 10)
            ->orderBy('amount')
            ->take(5)
            ->get();

        $recentSales = Sale::with(['customer', 'user'])
            ->latest('date')
            ->take(5)
            ->get();

        $periodStart = now()->startOfMonth()->subMonths(11);
        $periodEnd = now()->endOfMonth();
        $monthExpression = match (DB::connection()->getDriverName()) {
            'mysql' => "DATE_FORMAT(date, '%Y-%m')",
            'sqlite' => "strftime('%Y-%m', date)",
            default => throw new LogicException('El gráfico de ventas no admite este motor de base de datos.'),
        };
        $salesByMonth = Sale::query()
            ->whereBetween('date', [$periodStart, $periodEnd])
            ->selectRaw("$monthExpression as month, SUM(total) as total")
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlySales = [];

        foreach (range(0, 11) as $monthOffset) {
            $month = $periodStart->copy()->addMonths($monthOffset);
            $monthKey = $month->format('Y-m');

            $monthlySales[] = [
                'label' => $month->format('m/Y'),
                'total' => (float) ($salesByMonth[$monthKey] ?? 0),
            ];
        }

        return view('principal.dashboard.index', compact(
            'salesToday',
            'salesMonth',
            'customersCount',
            'medicinesCount',
            'lowStockCount',
            'lowStockMedicines',
            'recentSales',
            'monthlySales',
        ));
    }
}
