<!doctype html>
<html lang="es" dir="ltr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="San Juan – Sistema de Venta de Medicamentos">
  <title>FARM SJB | @yield('title')</title>
  
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>

<body>

<div id="app-wrapper" class="d-flex">

  <!-- AQUÍ LLAMAMOS AL SIDEBAR DIVIDIDO -->
  @include('partials.sidebar')

  <div class="main-wrapper d-flex flex-column flex-grow-1 overflow-hidden">
    
    <!-- AQUÍ LLAMAMOS AL HEADER DIVIDIDO -->
    @include('partials.header')

    <main class="app-content flex-grow-1 overflow-y-auto p-3 p-lg-4">
      <div class="container-fluid p-0">
        <!-- AQUÍ SE INYECTARÁ EL CONTENIDO DE CADA VISTA (COMO CATEGORÍAS) -->
        @yield('content')
      </div>
    </main>

  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>