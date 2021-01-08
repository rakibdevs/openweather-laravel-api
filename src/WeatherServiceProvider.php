<?php

namespace RakibDevs\Weather;

use Illuminate\Support\ServiceProvider;

class WeatherServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('RakibDevs\Weather\Weather');
        $this->publishes([
            __DIR__ . '/config/openweather.php' => config_path('openweather.php'),
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
