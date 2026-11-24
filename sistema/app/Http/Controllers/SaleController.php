<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Detail;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    // Listado de ventas (con cliente y vendedor cargados)
    public function index()
    {
        $sales = Sale::with(['customer', 'user'])->latest('date')->get();

        return view('management.sales.index', compact('sales'));
    }

    // Formulario para registrar una venta
    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $medicines = Medicine::with('category')
            ->where('amount', '>', 0)
            ->orderByDesc('id')
            ->get();

        return view('management.sales.create', compact('customers', 'users', 'medicines'));
    }

    // Guardar la venta con sus detalles dentro de una transacción
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'users_id' => ['required', 'exists:users,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicines_id' => ['required', 'distinct', 'exists:medicines,id'],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Debe agregar al menos un medicamento.',
            'items.*.medicines_id.distinct' => 'No repita el mismo medicamento, aumente la cantidad.',
        ]);

        $sale = DB::transaction(function () use ($request) {
            // 1. Crear la venta (el total e IGV se actualizan al final)
            $sale = Sale::create([
                'customer_id' => $request->customer_id,
                'users_id' => $request->users_id,
                'date' => now(),
                'total' => 0,
                'igv' => 0,
            ]);

            $total = 0;

            // 2. Registrar cada medicamento vendido y descontar stock
            foreach ($request->items as $item) {
                $medicine = Medicine::lockForUpdate()->find($item['medicines_id']);

                if ($medicine->amount < $item['amount']) {
                    throw ValidationException::withMessages([
                        'items' => "Stock insuficiente para medicamento #{$medicine->id} (disponible: {$medicine->amount}).",
                    ]);
                }

                $subtotal = round((float) $medicine->price_unit * (int) $item['amount'], 2);

                Detail::create([
                    'sales_id' => $sale->id,
                    'medicines_id' => $medicine->id,
                    'amount' => (int) $item['amount'],
                    'price_unit' => $medicine->price_unit,
                    'subtotal' => $subtotal,
                ]);

                $medicine->decrement('amount', (int) $item['amount']);
                $total += $subtotal;
            }

            $total = round($total, 2);

            // 3. Guardar total e IGV (los precios ya incluyen el 18% de IGV)
            $sale->update([
                'total' => $total,
                'igv' => round($total * 18 / 118, 2),
            ]);

            return $sale;
        });

        return to_route('sales.show', $sale)->with('ok', 'Venta registrada');
    }

    // Ver el detalle de una venta
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'user', 'details.medicine.category']);

        return view('management.sales.show', compact('sale'));
    }

    // Anular venta: devuelve el stock y elimina la venta (y sus detalles por cascadeOnDelete)
    public function destroy(Sale $sale)
    {
        DB::transaction(function () use ($sale) {
            foreach ($sale->details as $detail) {
                Medicine::where('id', $detail->medicines_id)
                    ->increment('amount', $detail->amount);
            }

            $sale->delete();
        });

        return to_route('sales.index')->with('ok', 'Venta anulada y stock devuelto');
    }
}