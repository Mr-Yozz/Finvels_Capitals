<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Loan;
use App\Observers\LoanObserver;

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
        Loan::observe(LoanObserver::class);
    }
}
