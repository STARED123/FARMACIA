<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@sanjuan.com')->first();
        $totalVentas = Sale::count();

        if ($admin && class_exists(Report::class)) {
            Report::firstOrCreate(
                ['title' => 'Reporte Inicial de Ventas'],
                [
                    'user_id' => $admin->id,
                    'total_sales' => $totalVentas,
                    'generated_at' => now(),
                ]
            );
        }
    }
}