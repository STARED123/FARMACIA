<header class="navbar navbar-expand bg-white border-bottom px-4 py-3">
    <div class="container-fluid p-0">
        <!-- Botón para colapsar sidebar o título de sección -->
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-light border-0" type="button" id="sidebar-toggle" data-sidebar-toggle aria-label="Alternar menú lateral">
                <i class="bi bi-list fs-5"></i>
            </button>
            <h4 class="mb-0 fw-bold text-dark">@yield('name_section')</h4>
        </div>

        <!-- Notificaciones y perfil derecho -->
        <ul class="navbar-nav align-items-center gap-3">
            <li class="nav-item dropdown">
                <button type="button" class="btn btn-light border-0 text-dark position-relative" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Notificaciones">
                    <i class="bi bi-bell fs-5"></i>
                    @if ($lowStockMedicines->isNotEmpty())
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            {{ $lowStockMedicines->count() }}
                            <span class="visually-hidden">notificaciones de stock bajo</span>
                        </span>
                    @endif
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0" style="width: 320px;">
                    <div class="px-3 py-2 border-bottom">
                        <strong>Stock bajo</strong>
                    </div>
                    @forelse ($lowStockMedicines as $medicine)
                        <a class="dropdown-item py-2" href="{{ route('medicines.index', ['low_stock' => 1]) }}">
                            <div class="d-flex justify-content-between gap-2">
                                <span>{{ $medicine->category->name ?? 'Medicamento #' . $medicine->id }}</span>
                                <span class="fw-bold text-danger">{{ $medicine->amount }}</span>
                            </div>
                        </a>
                    @empty
                        <span class="dropdown-item-text text-muted py-3">No hay alertas de stock.</span>
                    @endforelse
                </div>
            </li>
        </ul>
    </div>
</header>