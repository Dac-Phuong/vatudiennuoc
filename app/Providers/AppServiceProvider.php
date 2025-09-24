<?php
namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share([
            'user'       => fn()       => auth()->user(),
            'categories' => fn() => \App\Models\ProductCategory::all(),
            'settings'   => fn()   => Setting::all()->pluck('value', 'key')->toArray(),
        ]);

    }
}
