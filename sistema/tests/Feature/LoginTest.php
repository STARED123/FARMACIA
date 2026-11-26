<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_form_shows_the_prototype_and_session_message(): void
    {
        $this->withSession(['ok' => 'Cerraste sesión correctamente.'])
            ->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('action="'.route('login').'" method="POST"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="remember"', false)
            ->assertSee('Cerraste sesión correctamente.')
            ->assertDontSee('id="loginForm"', false)
            ->assertDontSee('app.js')
            ->assertDontSee('preventDefault');
    }

    public function test_seeded_admin_can_log_in_with_the_acceptance_credentials(): void
    {
        $this->seed();

        $this->post(route('login'), [
            'email' => 'admin@sanjuan.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard.index'));

        $this->assertAuthenticatedAs(User::where('email', 'admin@sanjuan.com')->first());
        $this->get(route('dashboard.index'))->assertOk();
    }

    public function test_database_seeders_can_be_run_more_than_once(): void
    {
        $this->artisan('db:seed')->assertExitCode(0);
        $this->artisan('db:seed')->assertExitCode(0);

        $this->assertDatabaseCount('roles', 3);
        $this->assertDatabaseCount('users', 4);
    }

    public function test_category_seeder_can_be_run_more_than_once(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(CategorySeeder::class);

        $this->assertDatabaseCount('categories', 8);
    }

    public function test_invalid_credentials_return_to_login_with_only_the_email(): void
    {
        $this->seed();

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'admin@sanjuan.com',
                'password' => 'incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'El correo o la contraseña no son correctos.',
            ])
            ->assertSessionHas('_old_input', [
                'email' => 'admin@sanjuan.com',
            ]);
    }

    public function test_login_validates_email_and_password(): void
    {
        $this->from(route('login'))
            ->post(route('login'), [
                'email' => 'correo-invalido',
                'password' => '',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_logout_invalidates_the_authenticated_session(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'admin@sanjuan.com')->first())
            ->withSession(['temporary-value' => 'remove'])
            ->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('ok', 'Cerraste sesión correctamente.')
            ->assertSessionMissing('temporary-value');

        $this->assertGuest();
    }

    public function test_guests_are_redirected_to_login_from_protected_pages(): void
    {
        $this->get(route('dashboard.index'))->assertRedirect(route('login'));
        $this->get(route('medicines.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_redirected_from_login_to_dashboard(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($user)
            ->get(route('login'))
            ->assertRedirect(route('dashboard.index'));
    }

    public function test_sidebar_shows_the_authenticated_users_details_and_role(): void
    {
        $this->seed();
        $user = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($user)
            ->view('partials.sidebar')
            ->assertSee(mb_strtoupper(mb_substr($user->name, 0, 2)))
            ->assertSee($user->name)
            ->assertSee($user->email)
            ->assertSee($user->role->name);
    }

    public function test_logout_modal_posts_to_the_logout_route_with_csrf(): void
    {
        $this->view('partials.modal-logout')
            ->assertSee('action="'.route('logout').'" method="POST"', false)
            ->assertSee('name="_token"', false)
            ->assertSee('>Salir</button>', false)
            ->assertDontSee('id="confirmLogoutBtn"', false);
    }

    public function test_sidebar_logout_link_opens_the_logout_modal(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($administrator)
            ->view('partials.sidebar')
            ->assertSee('data-bs-toggle="modal"', false)
            ->assertSee('data-bs-target="#logoutModal"', false);
    }
}
