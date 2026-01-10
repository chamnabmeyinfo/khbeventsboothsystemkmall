<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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
        // Fix: Ensure database prefix is always a string, not an array
        $prefix = Config::get('database.connections.mysql.prefix', '');
        if (!is_string($prefix)) {
            Config::set('database.connections.mysql.prefix', is_array($prefix) ? '' : (string)$prefix);
        }
    }
}
