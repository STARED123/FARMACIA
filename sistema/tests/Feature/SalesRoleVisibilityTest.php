<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesRoleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_sees_only_dashboard_customers_and_sales_in_sidebar(): void
    {
        $this->seed();
        $cashier = User::where('email', 'cmendoza@sanjuan.com')->firstOrFail();

        $this->actingAs($cashier)
            ->view('partials.sidebar')
            ->assertSee('Dashboard')
            ->assertSee('Clientes')
            ->assertSee('Ventas')
            ->assertDontSee('Reportes')
            ->assertDontSee('href="'.route('medicines.index').'"', false)
            ->assertDontSee('Configuraciones')
            ->assertDontSee('Roles')
            ->assertDontSee('Usuarios')
            ->assertDontSee('Categorías');
    }

    public function test_pharmacist_sees_reports_and_medicines_but_not_configurations(): void
    {
        $this->seed();
        $pharmacist = User::where('email', 'rquispe@sanjuan.com')->firstOrFail();

        $this->actingAs($pharmacist)
            ->view('partials.sidebar')
            ->assertSee('Reportes')
            ->assertSee('Medicamentos')
            ->assertDontSee('Configuraciones')
            ->assertDontSee('Roles')
            ->assertDontSee('Usuarios')
            ->assertDontSee('Categorías');
    }

    public function test_administrator_sees_all_sidebar_groups(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($administrator)
            ->view('partials.sidebar')
            ->assertSee('Dashboard')
            ->assertSee('Reportes')
            ->assertSee('Medicamentos')
            ->assertSee('Clientes')
            ->assertSee('Ventas')
            ->assertSee('Configuraciones')
            ->assertSee('Roles')
            ->assertSee('Usuarios')
            ->assertSee('Categorías');
    }

    public function test_cashier_does_not_see_the_cancel_sale_action(): void
    {
        $this->seed();
        $cashier = User::where('email', 'cmendoza@sanjuan.com')->firstOrFail();

        $this->actingAs($cashier)
            ->view('management.sales.index', ['sales' => collect([$this->saleFor($cashier)])])
            ->assertSee('Ver detalle')
            ->assertDontSee('Anular');
    }

    public function test_administrator_sees_the_cancel_sale_action(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($administrator)
            ->view('management.sales.index', ['sales' => collect([$this->saleFor($administrator)])])
            ->assertSee('Ver detalle')
            ->assertSee('Anular');
    }

    public function test_sale_form_shows_the_authenticated_seller_without_a_selector(): void
    {
        $this->seed();
        $cashier = User::where('email', 'cmendoza@sanjuan.com')->firstOrFail();

        $this->actingAs($cashier)
            ->get(route('sales.create'))
            ->assertOk()
            ->assertSee('value="Carlos Mendoza Ríos" disabled', false)
            ->assertDontSee('name="users_id"', false);
    }

    public function test_sale_uses_the_authenticated_user_instead_of_a_submitted_seller_id(): void
    {
        $this->seed();
        $cashier = User::where('email', 'cmendoza@sanjuan.com')->firstOrFail();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();
        $category = Category::create([
            'name' => 'Prueba',
            'description' => 'Categoría para la prueba',
        ]);
        $customer = Customer::create([
            'name' => 'Cliente de prueba',
            'dni' => '87654321',
            'phone' => '123456789',
        ]);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'amount' => 10,
            'price_unit' => 10,
            'subtotal' => 10,
        ]);

        $response = $this->actingAs($cashier)->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'users_id' => $administrator->id,
            'items' => [
                ['medicines_id' => $medicine->id, 'amount' => 1],
            ],
        ]);

        $sale = Sale::firstOrFail();
        $response->assertRedirect(route('sales.show', $sale));
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'users_id' => $cashier->id,
        ]);
        $this->get(route('sales.index'))->assertSee($cashier->name);
    }

    private function saleFor(User $user): Sale
    {
        $sale = new Sale([
            'date' => now(),
            'total' => 100,
            'igv' => 18,
        ]);
        $sale->setAttribute('id', 1);
        $sale->setRelation('customer', new Customer(['name' => 'Cliente de prueba']));
        $sale->setRelation('user', $user);

        return $sale;
    }
}
