<?php
namespace App\Http\Controllers;
use Illuminate\Contracts\View\View;
class DashboardController extends Controller
{
// Página principal del sistema (ruta / con nombre dashboard.index)
public function index(): View
{
// Busca resources/views/principal/dashboard/index.blade.php
return view('principal.dashboard.index');
}
}