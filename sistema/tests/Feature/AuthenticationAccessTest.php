<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;

class AuthenticationAccessTest extends TestCase
{
    public function test_guests_are_redirected_to_login_from_dashboard_and_medicines(): void
    {
        $this->get(route('dashboard.index'))
            ->assertRedirect(route('login'));

        $this->get(route('medicines.index'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_redirected_from_login_to_dashboard(): void
    {
        $this->actingAs(new User)
            ->get(route('login'))
            ->assertRedirect(route('dashboard.index'));
    }

    public function test_logout_closes_the_session_and_shows_a_confirmation(): void
    {
        $this->actingAs(new User)
            ->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('ok', 'Cerraste sesión correctamente.');

        $this->assertGuest();
    }

    public function test_sidebar_shows_the_authenticated_users_name_email_initials_and_role(): void
    {
        $user = new User([
            'name' => 'Ángela Pérez',
            'email' => 'angela@sanjuan.com',
        ]);
        $user->setRelation('role', new Role(['name' => 'Químico farmacéutico']));

        $this->actingAs($user)
            ->view('partials.sidebar')
            ->assertSee('ÁN')
            ->assertSee('Ángela Pérez')
            ->assertSee('angela@sanjuan.com')
            ->assertSee('Químico farmacéutico');
    }
}
