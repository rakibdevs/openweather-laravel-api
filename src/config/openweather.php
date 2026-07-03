<?php

return [

    /**
     * Get a free Open Weather Map API key
     * https://openweathermap.org/price.
     *
     */

    // Prefer the correctly spelled OPENWEATHER_API_KEY, but fall back to the
    // legacy misspelled OPENWAETHER_API_KEY so existing .env files keep working.
    'api_key' => env('OPENWEATHER_API_KEY', env('OPENWAETHER_API_KEY', "")),

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
     *
     * Note: `pollution_api_version` is the correctly spelled key. The legacy misspelled
     * `polution_api_version` is still honored as a fallback for previously published configs.
     */
    'pollution_api_version' => '2.5',

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

    // Prefer OPENWEATHER_API_LANG, falling back to the legacy misspelled OPENWAETHER_API_LANG.
    'lang' => env('OPENWEATHER_API_LANG', env('OPENWAETHER_API_LANG', 'en')),
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

    /**
     * Response Caching (optional)
     * --------------------------------------
     * When enabled, successful API responses are cached using Laravel's cache store
     * to reduce the number of outbound API calls. Disabled by default so behavior is
     * unchanged for existing users.
     *
     * cache_ttl is expressed in seconds.
     */

    'cache_enabled' => env('OPENWEATHER_CACHE_ENABLED', false),
    'cache_ttl' => env('OPENWEATHER_CACHE_TTL', 600),
];
