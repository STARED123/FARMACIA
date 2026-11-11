<?php

use App\Http\Controllers\CategoryController;

// Que la raíz abra directamente el índice de categorías en lugar de 'inicio'
Route::get('/', [CategoryController::class, 'index'])->name('inicio');

// Ruta para el CRUD de categorías
Route::resource('categories', CategoryController::class)->except(['show']);