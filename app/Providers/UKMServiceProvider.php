<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class UKMServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Add the UKM library path to the autoloader
        $this->app->booting(function() {
            $loader = require base_path('vendor/autoload.php');
            $loader->addPsr4('UKMNorge\\', '/etc/php-includes/UKM/');
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
