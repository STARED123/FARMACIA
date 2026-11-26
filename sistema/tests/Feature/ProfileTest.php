<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_open_their_profile(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('data-sidebar-toggle', false)
            ->assertSee('aria-label="Notificaciones"', false)
            ->assertSee('href="'.route('profile.edit').'"', false)
            ->assertSee('Guardar cambios');
    }

    public function test_authenticated_user_can_update_their_profile_without_changing_role(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@sanjuan.com')->firstOrFail();
        $roleId = $user->rol_id;

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'Administrador Actualizado',
                'dni' => $user->dni,
                'phone' => '999888777',
                'tuition' => $user->tuition,
                'email' => $user->email,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('ok', 'Perfil actualizado correctamente.');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Administrador Actualizado',
            'phone' => '999888777',
            'rol_id' => $roleId,
        ]);
    }

    public function test_guest_cannot_open_profile(): void
    {
        $this->get(route('profile.edit'))
            ->assertRedirect(route('login'));
    }
}
