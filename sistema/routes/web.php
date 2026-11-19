<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

// Vista raíz del sistema: el dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

// CRUD de categorías: crea 6 rutas (index, create, store, edit, update, destroy)
Route::resource('categories', CategoryController::class)->except('show');

// CRUD de roles: crea 6 rutas (index, create, store, edit, update, destroy)
Route::resource('roles', RoleController::class)->except('show');