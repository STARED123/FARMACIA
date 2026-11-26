@extends('template.administrador')

@section('title', 'Reportes')
@section('name_section', 'Reportes')
@section('description', 'Consulta y exporta las ventas y el inventario')
@section('item_section', 'Principal')
@section('route_section', route('reports.index'))
@section('section', 'Reportes')
@section('module', 'Ventas e inventario')

@section('content')
    @include('partials.alerts')

    <div class="card bg-white border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label for="start_date" class="form-label">Desde</label>
                    <input
                        type="date"
                        id="start_date"
                        name="start_date"
                        value="{{ old('start_date', $startDate?->format('Y-m-d')) }}"
                        class="form-control @error('start_date') is-invalid @enderror"
                    >
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="end_date" class="form-label">Hasta</label>
                    <input
                        type="date"
                        id="end_date"
                        name="end_date"
                        value="{{ old('end_date', $endDate?->format('Y-m-d')) }}"
                        class="form-control @error('end_date') is-invalid @enderror"
                    >
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>

            <div class="d-flex flex-wrap gap-2 mt-3">
                <a href="{{ route('reports.pdf', request()->only(['start_date', 'end_date'])) }}" class="btn btn-outline-danger">
                    Descargar PDF
                </a>
                <a href="{{ route('reports.excel', request()->only(['start_date', 'end_date'])) }}" class="btn btn-outline-success">
                    Descargar Excel
                </a>
            </div>
            <p class="small text-muted mt-3 mb-0">
                El filtro de fechas aplica a las ventas; el inventario muestra el estado actual.
            </p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6">
            <div class="card bg-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted text-uppercase">Ventas encontradas</div>
                    <div class="fs-4 fw-bold">{{ $sales->count() }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="card bg-white border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="small text-muted text-uppercase">Total vendido</div>
                    <div class="fs-4 fw-bold">S/ {{ number_format($salesTotal, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-white border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h2 class="h6 text-uppercase text-muted mb-0">Ventas</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>N.° venta</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">IGV</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->customer?->name ?? '—' }}</td>
                            <td>{{ $sale->user?->name ?? '—' }}</td>
                            <td class="text-end">S/ {{ number_format((float) $sale->total, 2) }}</td>
                            <td class="text-end">S/ {{ number_format((float) $sale->igv, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No hay ventas para el período seleccionado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card bg-white border-0 shadow-sm">
        <div class="card-header bg-white">
            <h2 class="h6 text-uppercase text-muted mb-0">Inventario actual</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th class="text-end">Existencias</th>
                        <th class="text-end">Precio unitario</th>
                        <th class="text-end">Subtotal registrado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventory as $medicine)
                        <tr>
                            <td>{{ $medicine->id }}</td>
                            <td>{{ $medicine->category?->name ?? '—' }}</td>
                            <td class="text-end">{{ $medicine->amount }}</td>
                            <td class="text-end">S/ {{ number_format((float) $medicine->price_unit, 2) }}</td>
                            <td class="text-end">S/ {{ number_format((float) $medicine->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No hay registros de inventario.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
