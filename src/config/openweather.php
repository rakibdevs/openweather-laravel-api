<?php

return [

    /**
     * Get a free Open Weather Map API key
     * https://openweathermap.org/price.
     *
     */

    'api_key' => env('OPENWAETHER_API_KEY', ""),

    /**
     * Current weather API endpoint : https://api.openweathermap.org/data/2.5/weather.
     * See documentation to get the correct version: https://openweathermap.org/current.
     */
    'weather_api_version' => '2.5',

    /**
     * Onecall API endpoint : https://api.openweathermap.org/data/2.5/onecall. Version 3.0 is available now.
     * See documentation : https://openweathermap.org/api/one-call-api
     */
    'onecall_api_version' => '2.5',

    /**
     * last 5 Days history API endpoint : https://api.openweathermap.org/data/2.5/onecall/timemachine.
     * See documentation : https://openweathermap.org/api/one-call-api#history
     */
    'historical_api_version' => '2.5',

    /**
     * Hourly forecast API endpoint https://api.openweathermap.org/data/2.5/forecast.
     * See documentation : https://openweathermap.org/forecast5.
     */
    'forecast_api_version' => '2.5',

    /**
     * Air pollution api endpoint : https://api.openweathermap.org/data/2.5/air_pollution.
     * See documentation : https://openweathermap.org/api/air-pollution.
     */
    'polution_api_version' => '2.5',

    /**
     * Geocoding API: https://openweathermap.org/api/geocoding-api
     */
    'geo_api_version' => '1.0',

    /**
     * Library Configuration
     *
     * https://openweathermap.org/current#multi
     *
     */

    'lang' => env('OPENWAETHER_API_LANG', 'en'),
    'date_format' => 'm/d/Y',
    'time_format' => 'h:i A',
    'day_format' => 'l',

    /**
     * Unit Configuration
     * --------------------------------------
     * Available units are c, f, k. (k is default)
     *
     * For temperature in Fahrenheit (f) and wind speed in miles/hour, use units=imperial
     * For temperature in Celsius (c) and wind speed in meter/sec, use units=metric
     */

    'temp_format' => 'c',
];
