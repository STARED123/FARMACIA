<!doctype html>
<html lang="es" dir="ltr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="San Juan – Sistema de Venta de Medicamentos">
{{-- Hueco 1: título de la pestaña --}}
<title>FARM SJB | @yield('title')</title>
{{-- asset(): rutas desde public/, funcionan en cualquier URL --}}
<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>
<div id="app-wrapper" class="d-flex">
{{-- Parcial: menú lateral --}}
@include('partials.sidebar')
<div class="main-wrapper d-flex flex-column flex-grow-1 overflow-hidden">
{{-- Parcial: encabezado (muestra el hueco name_section) --}}
@include('partials.header')
<main class="app-content flex-grow-1 overflow-y-auto p-3 p-lg-4">
<div class="container-fluid p-0">
<div class="d-flex align-items-center justify-content-between
flex-column flex-sm-row gap-2 mb-4">
{{-- Hueco: descripción de la página --}}
<p class="text-secondary mb-0">@yield('description')</p>
{{-- Breadcrumb (conserva el style del divisor): tres huecos --}}
<nav aria-label="Ruta de navegación">
<ol class="breadcrumb mb-0 small">
<li class="breadcrumb-item"><span>@yield('item_section')</span></li>
<li class="breadcrumb-item text-muted fw-medium" aria-current="page">
<a href="" class="text-decoration-none text-secondary">
@yield('section')
</a>
</li>
<li class="breadcrumb-item">
<span class="text-secondary">@yield('module')</span>
</li>
</ol>
</nav>
</div>
{{-- Hueco principal: el contenido propio de cada vista --}}
@yield('content')
</div>
</main>
</div>
</div>
{{-- Parcial: modal de cierre de sesión --}}
@include('partials.modal-logout')
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>