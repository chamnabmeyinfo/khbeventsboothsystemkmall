<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
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
        // Fix: Ensure database prefix is always a string, not an array
        $prefix = Config::get('database.connections.mysql.prefix', '');
        if (! is_string($prefix)) {
            Config::set('database.connections.mysql.prefix', is_array($prefix) ? '' : (string) $prefix);
        }

        // Online: force HTTPS and APP_URL so links/assets never point to localhost
        if ($this->app->environment('production') && ! $this->app->runningInConsole()) {
            $appUrl = config('app.url');
            if (! empty($appUrl)) {
                URL::forceRootUrl($appUrl);
                if (str_starts_with($appUrl, 'https://')) {
                    URL::forceScheme('https');
                }
            }
        }
    }
}
