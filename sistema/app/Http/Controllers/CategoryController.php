<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::withCount('medicines')
            ->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $datos = $request->validate([
            'name' => 'required|max:50|unique:categories',
            'description' => 'nullable|max:255',
        ], $this->mensajes());

        Category::create($datos);

        return to_route('categories.index')
            ->with('ok', 'Categoría registrada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $datos = $request->validate([
            'name' => [
                'required',
                'max:50',
                Rule::unique('categories')->ignore($category),
            ],
            'description' => 'nullable|max:255',
        ], $this->mensajes());

        $category->update($datos);

        return to_route('categories.index')
            ->with('ok', 'Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        if ($category->medicines()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene medicamentos registrados.');
        }

        $category->delete();

        return to_route('categories.index')
            ->with('ok', 'Categoría eliminada correctamente.');
    }

    private function mensajes(): array
    {
        return [
            'name.required' => 'Escribe el nombre de la categoría.',
            'name.max' => 'El nombre admite máximo 50 caracteres.',
            'name.unique' => 'Esa categoría ya está registrada.',
            'description.max' => 'La descripción admite máximo 255 caracteres.',
        ];
    }
}