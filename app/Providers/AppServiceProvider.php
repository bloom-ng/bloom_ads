<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\User;
use App\Observers\UserObserver;

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
        // Throw instead of silently dropping attributes missing from $fillable (outside production)
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());

        User::observe(UserObserver::class);

        Blade::component('modal', \App\View\Components\Modal::class);
    }
}
