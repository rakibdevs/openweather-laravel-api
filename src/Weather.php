<?php

namespace RakibDevs\Weather;

/**
 * Laravel OpenWeather API (openweather-laravel-api) is a Laravel package to connect Open Weather Map APIs ( https://openweathermap.org/api ) and access free API services easily.
 *
 * @package  openweather-laravel-api
 * @author   Md. Rakibul Islam <rakib1708@gmail.com>
 * @version  1.5
 * @since    2021-01-09
 */

use Illuminate\Support\Facades\Config;
use RakibDevs\Weather\Exceptions\InvalidConfiguration;

class Weather
{
    /**
     * current weather api endpoint : https://api.openweathermap.org/data/2.5/weather.
     * See documentation : https://openweathermap.org/current.
     *
     * @var string
     */

    protected $current = 'weather?';

    /**
     * onecall api endpoint : https://api.openweathermap.org/data/2.5/onecall.
     * See documentation : https://openweathermap.org/api/one-call-api
     *
     * @var string
     */

    protected $one_call = 'onecall?';

    /**
     * hourly forecast 5 Days 3 hour api endpoint : https://api.openweathermap.org/data/2.5/forecast.
     * See documentation : https://openweathermap.org/forecast5.
     *
     * @var string
     */

    protected $forecast = 'forecast?';

    /**
     * last 5 Days history api endpoint : https://api.openweathermap.org/data/2.5/onecall/timemachine.
     * See documentation : https://openweathermap.org/api/one-call-api#history
     *
     * @var string
     */

    protected $historical = 'onecall/timemachine?';

    /**
     * air pollution api endpoint : https://api.openweathermap.org/data/2.5/air_pollution.
     * See documentation : https://openweathermap.org/api/air-pollution.
     *
     * @var string
     */

    protected $air_pollution = 'air_pollution?';
    /**
     * temp_format available strings are c, f, k.
     *
     * @var string
     */


    protected $lang;

    /**
     * Units: available units are c, f, k.
     *
     * For temperature in Fahrenheit (f) and wind speed in miles/hour, use units=imperial
     * For temperature in Celsius (c) and wind speed in meter/sec, use units=metric
     * Temperature in Kelvin (k) and wind speed in meter/sec is used by default, so there is no need to use the units parameter in the API call if you want this
     *
     * @var array
     */
    protected $units = [
        'c' => 'metric',
        'f' => 'imperial',
        'k' => 'standard',
    ];

    protected $uom;

    protected $format;
 

    public function __construct()
    {
        self::setApi();
        self::setConfigParameters();
    }

    protected function setApi()
    {
        $this->api_key = config('openweather.api_key');
        if ($this->api_key == '') {
            throw InvalidConfiguration::apiKeyNotSpecified();
        }
    }


    protected function setConfigParameters()
    {
        $this->format = (object) config('openweather');
        $this->format->dt_format = $this->format->date_format.' '.$this->format->time_format;
        $this->uom = $this->units[$this->format->temp_format];
    }

    /**
     * build query parameters.
     *
     * @param array $params
     * @return string
     */

    private function params(array $params)
    {
        $params['appid'] = $this->api_key;
        $params['units'] = $this->uom;
        $params['lang'] = $this->format->lang;

        return http_build_query($params);
    }


    /**
     * Access current weather data for any location on Earth including over 200,000 cities! Open Weathe Map API collect and process weather data from different sources such as global and local weather models, satellites, radars and vast network of weather stations.
     * documentation : https://openweathermap.org/current.
     *
     * @param array $query
     *
     */


    private function getCurrent(array $query)
    {
        $route = $this->current.$this->params($query);
        $data = (new WeatherClient)->client()->fetch($route);

        return (new WeatherFormat($this->format))->formatCurrent($data);
    }

    /**
     * Make just one API call and get all your essential weather data for a specific location with OpenWeather One Call API.
     * documentation : https://openweathermap.org/api/one-call-api.
     *
     * @param array $query
     *
     */

    private function getOneCall(array $query)
    {
        $route = $this->one_call.$this->params($query);
        $data = (new WeatherClient)->client()->fetch($route);

        return (new WeatherFormat($this->format))->formatOneCall($data);
    }

    /**
     * 5 day forecast is available at any location or city. It includes weather forecast data with 3-hour step.
     * documentation : https://openweathermap.org/forecast5.
     *
     * @param array $query
     *
     */

    private function get3Hourly(array $query)
    {
        $route = $this->forecast.$this->params($query);
        $data = (new WeatherClient)->client()->fetch($route);

        return (new WeatherFormat($this->format))->format3Hourly($data);
    }

    /**
     * Historical weather data for the previous 5 days
     * documentation : https://openweathermap.org/api/one-call-api#history.
     *
     * @param array $query
     *
     */

    private function getHistorical(array $query)
    {
        $route = $this->historical.$this->params($query);
        $data = (new WeatherClient)->client()->fetch($route);

        return (new WeatherFormat($this->format))->formatHistorical($data);
    }

    /**
     * Air Pollution API concept
       Air Pollution API provides current, forecast and historical air pollution data for any coordinates on the globe

       Besides basic Air Quality Index, the API returns data about polluting gases, such as Carbon monoxide (CO), Nitrogen monoxide (NO), Nitrogen dioxide (NO2), Ozone (O3), Sulphur dioxide (SO2), Ammonia (NH3), and particulates (PM2.5 and PM10).

       Air pollution forecast is available for 5 days with hourly granularity. Historical data is accessible from 27th November 2020
     * documentation : https://openweathermap.org/api/air-pollution.
     *
     * @param array $query
     *
     */

    private function getAirPollution(array $query)
    {
        $route = $this->air_pollution.$this->params($query);
        $data = (new WeatherClient)->client()->fetch($route);

        return (new WeatherFormat($this->format))->formatHistorical($data);
    }


    /**
     * Geocoding API is a simple tool that we have developed to ease the search for locations while working with geographic names and coordinates.
     * documentation : https://openweathermap.org/api/geocoding-api.
     *
     * @param array $query
     *
     */

    private function getGeo(string $type, array $query)
    {
        $route = $type.$this->params($query);
        $data = (new WeatherClient)->client('geo')->fetch($route);

        return (new WeatherFormat($this->format))->formatGeo($data);
    }
    

    public function getCurrentByCity(string $city)
    {
        if (! is_numeric($city)) {
            $params['q'] = $city;
        } else {
            $params['id'] = $city;
        }

        return $this->getCurrent($params);
    }

    public function getCurrentByCord(string $lat, string $lon)
    {
        return $this->getCurrent([
            'lat' => $lat,
            'lon' => $lon,
        ]);
    }

    public function getCurrentByZip(string $zip, string $country = 'us')
    {
        return $this->getCurrent([
            'zip' => $zip,
            'country' => $country,
        ]);
    }

    public function getCurrentTempByCity(string $city)
    {
        if (! is_numeric($city)) {
            $params['q'] = $city;
        } else {
            $params['id'] = $city;
        }

        return $this->getCurrent($params)->main;
    }

    public function getOneCallByCord(string $lat, string $lon)
    {
        return $this->getOneCall([
            'lat' => $lat,
            'lon' => $lon,
        ]);
    }

    public function get3HourlyByCity(string $city)
    {
        if (! is_numeric($city)) {
            $params['q'] = $city;
        } else {
            $params['id'] = $city;
        }

        return $this->get3Hourly($params);
    }

    public function get3HourlyByZip(string $zip, string $country = 'us')
    {
        return $this->get3Hourly([
            'zip' => $zip,
            'country' => $country,
        ]);
    }

    public function get3HourlyByCord(string $lat, string $lon)
    {
        return $this->get3Hourly([
            'lat' => $lat,
            'lon' => $lon,
        ]);
    }

    public function getHistoryByCord(string $lat, string $lon, string $date)
    {
        return $this->getHistorical([
            'lat' => $lat,
            'lon' => $lon,
            'dt' => strtotime($date),
        ]);
    }

    public function getAirPollutionByCord(string $lat, string $lon, string $start = null, string $end = null)
    {
        return $this->getAirPollution([
            'lat' => $lat,
            'lon' => $lon,
            'start' => $start != null? strtotime($start):$start,
            'end' => $end != null? strtotime($end):$end,
        ]);
    }

    public function getGeoByCity(string $city, string  $limit = null)
    {
        $params['q'] = $city;
        if ($limit) {
            $params['limit'] = $limit;
        }

        return $this->getGeo('direct?', $params);
    }

    public function getGeoByCord(string $lat, string $lon, string $limit = null)
    {
        $params = [
            'lat' => $lat,
            'lon' => $lon,
        ];
        if ($limit) {
            $params['limit'] = $limit;
        }

        return $this->getGeo('reverse?', $params);
    }
}
