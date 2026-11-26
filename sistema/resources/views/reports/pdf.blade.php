<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de ventas e inventario</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #212529; }
        h1 { font-size: 20px; margin: 0 0 6px; }
        h2 { font-size: 14px; margin: 20px 0 8px; }
        p { margin: 0 0 12px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #dee2e6; padding: 6px; text-align: left; }
        th { background: #f1f3f5; }
        .number { text-align: right; }
        .summary { margin-bottom: 14px; }
    </style>
</head>
<body>
    <h1>Reporte de ventas e inventario</h1>
    <p>
        Período de ventas:
        @if ($startDate && $endDate)
            {{ $startDate->format('d/m/Y') }} al {{ $endDate->format('d/m/Y') }}
        @else
            Todas las fechas
        @endif
    </p>
    <p class="summary">
        Ventas encontradas: {{ $sales->count() }} |
        Total vendido: S/ {{ number_format($salesTotal, 2) }}
    </p>
    <p>El filtro de fechas aplica a las ventas; el inventario muestra el estado actual.</p>

    <h2>Ventas</h2>
    <table>
        <thead>
            <tr>
                <th>N.° venta</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Vendedor</th>
                <th class="number">Total (S/)</th>
                <th class="number">IGV (S/)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($sales as $sale)
                <tr>
                    <td>{{ $sale->id }}</td>
                    <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                    <td>{{ $sale->customer?->name ?? '—' }}</td>
                    <td>{{ $sale->user?->name ?? '—' }}</td>
                    <td class="number">{{ number_format((float) $sale->total, 2) }}</td>
                    <td class="number">{{ number_format((float) $sale->igv, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No hay ventas para el período seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Inventario actual</h2>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Categoría</th>
                <th class="number">Existencias</th>
                <th class="number">Precio unitario (S/)</th>
                <th class="number">Subtotal registrado (S/)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inventory as $medicine)
                <tr>
                    <td>{{ $medicine->id }}</td>
                    <td>{{ $medicine->category?->name ?? '—' }}</td>
                    <td class="number">{{ $medicine->amount }}</td>
                    <td class="number">{{ number_format((float) $medicine->price_unit, 2) }}</td>
                    <td class="number">{{ number_format((float) $medicine->subtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No hay registros de inventario.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
