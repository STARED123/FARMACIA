<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_seeded_admin_can_log_in_with_the_acceptance_credentials(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('rol_id');
            $table->string('name');
            $table->string('dni', 8)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $this->seed();

        $this->post(route('login'), [
            'email' => 'admin@sanjuan.com',
            'password' => 'password',
        ])
            ->assertRedirect(route('dashboard.index'));

        $this->assertAuthenticatedAs(User::where('email', 'admin@sanjuan.com')->first());
    }

    public function test_login_form_is_displayed(): void
    {
        $this->withoutMiddleware(RedirectIfAuthenticated::class)
            ->withSession(['ok' => 'Cerraste sesión correctamente.'])
            ->get(route('login'))
            ->assertOk()
            ->assertViewIs('auth.login')
            ->assertSee('name="email"', false)
            ->assertSee('name="remember"', false)
            ->assertSee('Cerraste sesión correctamente.')
            ->assertDontSee('id="loginForm"', false)
            ->assertDontSee('app.js')
            ->assertDontSee('preventDefault');
    }

    public function test_login_requires_an_email_and_password(): void
    {
        $this->from(route('login'))
            ->withoutMiddleware(RedirectIfAuthenticated::class)
            ->post(route('login'), [
                'email' => 'no-es-un-correo',
                'password' => '',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_valid_credentials_redirect_to_the_dashboard(): void
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'admin@sanjuan.com',
                'password' => 'password',
            ], true)
            ->andReturnTrue();

        $this->withoutMiddleware(RedirectIfAuthenticated::class)
            ->post(route('login'), [
                'email' => 'admin@sanjuan.com',
                'password' => 'password',
                'remember' => '1',
            ])
            ->assertRedirect(route('dashboard.index'));
    }

    public function test_invalid_credentials_return_to_login_with_only_the_email(): void
    {
        Auth::shouldReceive('attempt')
            ->once()
            ->with([
                'email' => 'admin@sanjuan.com',
                'password' => 'incorrecta',
            ], true)
            ->andReturnFalse();

        $this->from(route('login'))
            ->withoutMiddleware(RedirectIfAuthenticated::class)
            ->post(route('login'), [
                'email' => 'admin@sanjuan.com',
                'password' => 'incorrecta',
                'remember' => '1',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'El correo o la contraseña no son correctos.',
            ])
            ->assertSessionHas('_old_input', [
                'email' => 'admin@sanjuan.com',
            ]);
    }

    public function test_logout_invalidates_the_session_and_redirects_with_a_message(): void
    {
        Auth::shouldReceive('logout')->once();

        $this->withoutMiddleware(Authenticate::class)
            ->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('ok', 'Cerraste sesión correctamente.');
    }
}
