<?php

namespace App\Providers;

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
    public function boot()
    {
        // Forzar HTTPS si la petición es a ngrok, localtunnel o cloudflare tunnel
        $host = request()->getHost();
        if ($host && (
            str_contains($host, 'ngrok-free.dev') ||
            str_contains($host, 'loca.lt') ||
            str_contains($host, 'trycloudflare.com')
        )) {
            \URL::forceScheme('https');
        }
    }
}
