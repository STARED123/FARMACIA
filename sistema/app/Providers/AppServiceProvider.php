<?php

namespace App\Providers;

use App\Models\Medicine;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.header', function ($view): void {
            $view->with([
                'lowStockMedicines' => Medicine::with('category')
                    ->where('amount', '<=', 10)
                    ->orderBy('amount')
                    ->take(5)
                    ->get(),
            ]);
        });
    }
}
