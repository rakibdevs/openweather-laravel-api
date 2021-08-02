<?php

namespace RakibDevs\Weather;

use Illuminate\Support\ServiceProvider;
use RakibDevs\Weather\Weather;

class WeatherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make(Weather::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config/openweather.php' => config_path('openweather.php'),
        ]);
    }
}
