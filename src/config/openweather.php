<?php

return [

    /**
     * Get a free Open Weather Map API key : https://openweathermap.org/price.
     *
     */

    'api_key' => '', // get an API key
    

    /**
     *
     * Library https://openweathermap.org/current#multi
     *  af - Afrikaans
        al - Albanian
        ar - Arabic
        az - Azerbaijani
        bg - Bulgarian
        ............
        more availabe language
     *
     */

    'lang' => 'en',

    'date_format' => 'm/d/Y',
    'time_format' => 'h:i A',
    'day_format' => 'l',

    /**
     * Units: available units are c, f, k.
     *
     * For temperature in Fahrenheit (f) and wind speed in miles/hour, use units=imperial
     * For temperature in Celsius (c) and wind speed in meter/sec, use units=metric
     * Temperature in Kelvin (k) and wind speed in meter/sec is used by default, so there is no need to use the units parameter in the API call if you want this
     *
     */

    'temp_format' => 'c',
];
