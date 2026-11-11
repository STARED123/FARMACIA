@extends('template.administrador')

@section('title', 'Listado de Categorías')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-bold">Gestión de Categorías</h1>
    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">Nueva Categoría</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <p>Aquí irá la tabla de tu CRUD de categorías.</p>
    </div>
</div>
@endsection