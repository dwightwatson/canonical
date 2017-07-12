<?php

namespace Watson\Canonical;

use Illuminate\Support\ServiceProvider;

class CanonicalServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config.php', 'canonical'
        );
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('canonical.php'),
        ], 'config');
    }
}
