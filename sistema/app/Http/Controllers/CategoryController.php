<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Regla unique que ignora un registro

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // SELECT * FROM categories
        $categories = Category::all();

        // compact() envía $categories a la vista con el mismo nombre
        return view('settings.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Muestra el formulario vacío
        return view('settings.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Reglas de validación: string('name', 50)->unique()
        $validated = $request->validate([
            'name' => 'required|max:50|unique:categories',
            'description' => 'nullable|max:255',
        ]);

        // INSERT INTO categories
        Category::create($validated);

        // PRG: redirige al listado con un mensaje flash
        return to_route('categories.index')
            ->with('ok', 'Categoría registrada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        // Muestra el formulario con los datos actuales
        // Route model binding: Laravel busca la categoría con el id de la URL
        return view('settings.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // UPDATE: valida, actualiza y redirige
        $validated = $request->validate([
            'name' => [
                'required',
                'max:50',
                // Único, sin contar la categoría que se está editando
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
            'description' => 'nullable|max:255',
        ]);

        // UPDATE categories SET ... WHERE id = ?
        $category->update($validated);

        return to_route('categories.index')
            ->with('ok', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // DELETE (versión 1, sin verificar)
        $category->delete();
        return to_route('categories.index')
            ->with('ok', 'Categoría eliminada correctamente.');
    }
}