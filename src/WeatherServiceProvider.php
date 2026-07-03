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
        $this->mergeConfigFrom(__DIR__ . '/config/openweather.php', 'openweather');

        $this->app->singleton(Weather::class, function () {
            return new Weather();
        });

        $this->app->alias(Weather::class, 'weather');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/openweather.php' => config_path('openweather.php'),
            ], 'config');
        }
    }
}
