<?php

namespace RakibDevs\Weather\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static object getCurrentByCity(string $city)
 * @method static object getCurrentByCord(string $lat, string $lon)
 * @method static object getCurrentByZip(string $zip, string $country = 'us')
 * @method static object getCurrentTempByCity(string $city)
 * @method static object getOneCallByCord(string $lat, string $lon)
 * @method static object get3HourlyByCity(string $city)
 * @method static object get3HourlyByZip(string $zip, string $country = 'us')
 * @method static object get3HourlyByCord(string $lat, string $lon)
 * @method static object getHistoryByCord(string $lat, string $lon, string $date)
 * @method static object getAirPollutionByCord(string $lat, string $lon, ?string $start = null, ?string $end = null)
 * @method static object getGeoByCity(string $city, ?string $limit = null)
 * @method static object getGeoByCord(string $lat, string $lon, ?string $limit = null)
 * @method static \RakibDevs\Weather\Weather units(string $unit)
 * @method static \RakibDevs\Weather\Weather lang(string $lang)
 * @method static \RakibDevs\Weather\Weather fluent(bool $fluent = true)
 *
 * @see \RakibDevs\Weather\Weather
 */
class Weather extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'weather';
    }
}
