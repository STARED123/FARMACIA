@extends('layouts.app')

@section('content')
@php
    $fechaInicio =$fechaInicio ?? now()->startOfMonth()->toDateString();
    $fechaFin =$fechaFin ?? now()->endOfMonth()->toDateString();
    $ventas =$ventas ?? collect();
    $totalIngresos =$totalIngresos ?? 0;
    $cantidadVentas =$cantidadVentas ?? $ventas->count();$ticketPromedio = $ticketPromedio ?? ($cantidadVentas > 0 ? $totalIngresos / $cantidadVentas : 0);
    $topMedicamentos =$topMedicamentos ?? collect();
    $stockBajo =$stockBajo ?? collect();
@endphp

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Reportes de Farmacia</h2>
        <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
            Imprimir / Guardar PDF
        </button>
    </div>

    {{-- Filtro por fechas --}}
    <form method="GET" action="{{ route('reports.index') }}" class="card shadow-sm mb-4">
        <div class="card-body row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" class="form-control">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-danger w-100">Filtrar Reporte</button>
            </div>
        </div>
    </form>

    {{-- Tarjetas resumen --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <span class="text-muted small">Ingresos Totales</span>
                    <h3 class="fw-bold text-success mt-1">S/ {{ number_format((float) $totalIngresos, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <span class="text-muted small">Ventas Realizadas</span>
                    <h3 class="fw-bold mt-1">{{ $cantidadVentas }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <span class="text-muted small">Ticket Promedio</span>
                    <h3 class="fw-bold text-primary mt-1">S/ {{ number_format((float) $ticketPromedio, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Top medicamentos --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Top 5 Medicamentos Más Vendidos</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Medicamento</th>
                                <th class="text-center">Unidades</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topMedicamentos as$med)
                                <tr>
                                    <td>{{ $med->name ?? '—' }}</td>
                                    <td class="text-center">{{ $med->total_vendido ?? 0 }}</td>
                                    <td class="text-end">S/ {{ number_format((float) ($med->total_recaudado ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-3">Sin ventas en este periodo</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Alertas de stock bajo --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold text-danger">Alerta de Stock Bajo (&le; 10)</div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Medicamento</th>
                                <th class="text-center">Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockBajo as$item)
                                <tr>
                                    <td>{{ $item->name ?? '—' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $item->stock ?? 0 }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-3">Todo el inventario tiene stock saludable</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Detalle de ventas del periodo --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold">Historial de Ventas</div>
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ventas as$venta)
                        <tr>
                            <td>{{ $venta->id }}</td>
                            <td>{{ optional($venta->created_at)->format('d/m/Y H:i') ?? '—' }}</td>
                            <td>{{ $venta->customer->name ?? 'Cliente General' }}</td>
                            <td>{{ $venta->user->name ?? '—' }}</td>
                            <td class