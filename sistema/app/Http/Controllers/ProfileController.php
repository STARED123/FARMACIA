<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('settings.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'dni' => ['required', 'digits:8', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'digits:9'],
            'tuition' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (blank($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return to_route('profile.edit')->with('ok', 'Perfil actualizado correctamente.');
    }
}
