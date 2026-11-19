<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    // READ: listado
    public function index()
    {
        $roles = Role::all();
        return view('settings.roles.index', compact('roles'));
    }

    // Formulario vacío
    public function create()
    {
        return view('settings.roles.create');
    }

    // CREATE: validar, guardar, redirigir
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50|unique:roles',
            'description' => 'nullable|max:255',
        ]);

        Role::create($validated);

        return to_route('roles.index')
            ->with('ok', 'Rol registrado correctamente.');
    }

    // Formulario lleno (route model binding)
    public function edit(Role $role)
    {
        return view('settings.roles.edit', compact('role'));
    }

    // UPDATE: validar, actualizar, redirigir
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'max:50',
                Rule::unique('roles', 'name')->ignore($role->id),
            ],
            'description' => 'nullable|max:255',
        ]);

        $role->update($validated);

        return to_route('roles.index')
            ->with('ok', 'Rol actualizado correctamente.');
    }

    // DELETE: eliminar rol
    public function destroy(Role $role)
    {
        $role->delete();

        return to_route('roles.index')
            ->with('ok', 'Rol eliminado correctamente.');
    }
}