<?php

namespace Tests\Feature;

use App\Exports\InventoryReportSheet;
use App\Exports\ReportsExport;
use App\Exports\SalesReportSheet;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReportsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_exposes_twelve_month_sales_totals(): void
    {
        $this->travelTo(CarbonImmutable::parse('2026-03-15 12:00:00'));
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();
        $customer = $this->createCustomer('Cliente dashboard', '12345678');

        $this->createSale($administrator, $customer, '2026-03-02 10:00:00', 120);
        $this->createSale($administrator, $customer, '2026-03-11 10:00:00', 30);
        $this->createSale($administrator, $customer, '2025-03-31 10:00:00', 99);

        $response = $this->actingAs($administrator)->get(route('dashboard.index'));

        $response->assertOk()->assertSee('monthly-sales-chart');

        $monthlySales = $response->viewData('monthlySales');
        $this->assertCount(12, $monthlySales);
        $this->assertSame(['label' => '04/2025', 'total' => 0.0], $monthlySales[0]);
        $this->assertSame(['label' => '03/2026', 'total' => 150.0], $monthlySales[11]);
    }

    public function test_filtered_report_contains_matching_sales_and_current_inventory(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();
        $insideCustomer = $this->createCustomer('Cliente dentro del período', '12345678');
        $outsideCustomer = $this->createCustomer('Cliente fuera del período', '87654321');
        $this->createSale($administrator, $insideCustomer, '2026-05-10 10:00:00', 30);
        $this->createSale($administrator, $outsideCustomer, '2026-06-01 10:00:00', 40);
        $category = Category::create(['name' => 'Vitaminas', 'description' => 'Categoría de prueba']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'amount' => 8,
            'price_unit' => 5.5,
            'subtotal' => 44,
        ]);

        $response = $this->actingAs($administrator)->get(route('reports.index', [
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]));

        $response->assertOk()
            ->assertSee($insideCustomer->name)
            ->assertDontSee($outsideCustomer->name)
            ->assertSee('Vitaminas')
            ->assertSee((string) $medicine->id)
            ->assertSee('30.00')
            ->assertSee('El filtro de fechas aplica a las ventas; el inventario muestra el estado actual.');
    }

    public function test_cashier_is_forbidden_from_all_report_endpoints(): void
    {
        $this->seed();
        $cashier = User::where('email', 'cmendoza@sanjuan.com')->firstOrFail();

        $this->actingAs($cashier);

        $this->get(route('reports.index'))->assertForbidden();
        $this->get(route('reports.pdf'))->assertForbidden();
        $this->get(route('reports.excel'))->assertForbidden();
    }

    #[DataProvider('authorizedUsers')]
    public function test_administrator_and_pharmacist_can_open_the_report_page(string $email): void
    {
        $this->seed();
        $user = User::where('email', $email)->firstOrFail();

        $this->actingAs($user)->get(route('reports.index'))->assertOk();
    }

    public static function authorizedUsers(): array
    {
        return [
            'administrator' => ['admin@sanjuan.com'],
            'pharmacist' => ['rquispe@sanjuan.com'],
        ];
    }

    public function test_report_date_range_requires_both_dates(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($administrator)
            ->from(route('reports.index'))
            ->get(route('reports.index', ['start_date' => '2026-05-01']))
            ->assertRedirect(route('reports.index'))
            ->assertSessionHasErrors([
                'end_date' => 'Indica ambas fechas para aplicar el filtro.',
            ]);
    }

    public function test_report_rejects_an_end_date_before_the_start_date(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($administrator)
            ->from(route('reports.index'))
            ->get(route('reports.index', [
                'start_date' => '2026-05-02',
                'end_date' => '2026-05-01',
            ]))
            ->assertRedirect(route('reports.index'))
            ->assertSessionHasErrors([
                'end_date' => 'La fecha final debe ser igual o posterior a la fecha inicial.',
            ]);
    }

    public function test_pdf_report_is_downloaded(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($administrator)
            ->get(route('reports.pdf'))
            ->assertDownload('reporte-farmacia.pdf');
    }

    public function test_excel_export_applies_date_range_to_sales_and_includes_inventory(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();
        $insideCustomer = $this->createCustomer('Cliente exportado', '12345678');
        $outsideCustomer = $this->createCustomer('Cliente excluido', '87654321');
        $insideSale = $this->createSale($administrator, $insideCustomer, '2026-05-10 10:00:00', 30);
        $this->createSale($administrator, $outsideCustomer, '2026-06-01 10:00:00', 40);
        $category = Category::create(['name' => 'Analgésicos', 'description' => 'Categoría de prueba']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'amount' => 12,
            'price_unit' => 2.5,
            'subtotal' => 30,
        ]);
        Excel::fake();

        $response = $this->actingAs($administrator)->get(route('reports.excel', [
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-31',
        ]));

        $response->assertOk();
        Excel::assertDownloaded('reporte-farmacia.xlsx', function (ReportsExport $export) use ($insideSale, $medicine): bool {
            [$salesSheet, $inventorySheet] = $export->sheets();

            return $salesSheet instanceof SalesReportSheet
                && $inventorySheet instanceof InventoryReportSheet
                && $salesSheet->collection()->modelKeys() === [$insideSale->id]
                && $inventorySheet->collection()->modelKeys() === [$medicine->id];
        });
    }

    public function test_excel_report_download_creates_a_workbook(): void
    {
        $this->seed();
        $administrator = User::where('email', 'admin@sanjuan.com')->firstOrFail();

        $this->actingAs($administrator)
            ->get(route('reports.excel'))
            ->assertDownload('reporte-farmacia.xlsx');
    }

    private function createCustomer(string $name, string $dni): Customer
    {
        return Customer::create([
            'name' => $name,
            'dni' => $dni,
            'phone' => '9'.substr($dni, -8),
        ]);
    }

    private function createSale(User $user, Customer $customer, string $date, float $total): Sale
    {
        return Sale::create([
            'users_id' => $user->id,
            'customer_id' => $customer->id,
            'date' => $date,
            'total' => $total,
            'igv' => round($total * 18 / 118, 2),
        ]);
    }

    public function test_unauthenticated_user_is_redirected_to_login_from_reports(): void
    {
        $this->get(route('reports.index'))->assertRedirect(route('login'));
    }
}
