<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (windows_os()) {
            foreach (['PATH', 'SYSTEMROOT'] as $key) {
                if (! isset($_ENV[$key]) && ($value = getenv($key)) !== false) {
                    $_ENV[$key] = $value;
                }
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || request()->isSecure()) {
            URL::forceScheme('https');
        }
    }
}
