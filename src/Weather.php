<?php

namespace RakibDevs\Weather;

use GuzzleHttp\Client;

/**
 * Laravel OpenWeather API (openweather-laravel-api) is a Laravel package to connect Open Weather Map APIs ( https://openweathermap.org/api ) and access free API services easily.
 *
 * @package  openweather-laravel-api
 * @author   Md. Rakibul Islam <rakib1708@gmail.com>
 * @since    2021-01-09
 */

class Weather
{
    /**
     * Optional Guzzle client, primarily used for dependency injection and testing.
     *
     * @var \GuzzleHttp\Client|null
     */
    protected $httpClient;

    /**
     * Per-call configuration overrides (e.g. units, lang) applied to the next request.
     *
     * @var array
     */
    protected $overrides = [];

    /**
     * When true, the next response is returned as a fluent WeatherResponse instead of stdClass.
     *
     * @var bool
     */
    protected $fluent = false;

    /**
     * @param \GuzzleHttp\Client|null $httpClient
     */
    public function __construct(?Client $httpClient = null)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Override the temperature/units format for the next request only.
     * Accepts c (metric), f (imperial) or k (standard).
     *
     * @param string $unit
     * @return $this
     */
    public function units(string $unit): self
    {
        $this->overrides['temp_format'] = $unit;

        return $this;
    }

    /**
     * Override the response language for the next request only.
     *
     * @param string $lang
     * @return $this
     */
    public function lang(string $lang): self
    {
        $this->overrides['lang'] = $lang;

        return $this;
    }

    /**
     * Return the next response wrapped in a fluent WeatherResponse object
     * (dot-notation access, toArray/toJson/collect, convenience getters).
     * Property access such as $res->main->temp keeps working. Applies to a
     * single request and then resets. By default responses stay stdClass.
     *
     * @param bool $fluent
     * @return $this
     */
    public function fluent(bool $fluent = true): self
    {
        $this->fluent = $fluent;

        return $this;
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

    public function getAirPollutionByCord(string $lat, string $lon, ?string $start = null, ?string $end = null)
    {
        return $this->getAirPollution([
            'lat' => $lat,
            'lon' => $lon,
            'start' => $start != null ? strtotime($start) : $start,
            'end' => $end != null ? strtotime($end) : $end,
        ]);
    }

    public function getGeoByCity(string $city, ?string $limit = null)
    {
        $params['q'] = $city;
        if ($limit) {
            $params['limit'] = $limit;
        }

        return $this->getGeo('direct?', $params);
    }

    public function getGeoByCord(string $lat, string $lon, ?string $limit = null)
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

    /**
     * Build a WeatherClient carrying any per-call overrides and the (optional) injected
     * Guzzle client, then reset the overrides so they only apply to a single request.
     *
     * @return \RakibDevs\Weather\WeatherClient
     */
    protected function makeClient(): WeatherClient
    {
        $client = new WeatherClient($this->overrides, $this->httpClient);
        $this->overrides = [];

        return $client;
    }

    /**
     * Wrap the response in a WeatherResponse when fluent mode was requested for this
     * call, then reset the flag so it only affects a single request.
     *
     * @param mixed $data
     * @return mixed
     */
    protected function respond($data)
    {
        $fluent = $this->fluent;
        $this->fluent = false;

        return $fluent ? new WeatherResponse($data) : $data;
    }

    /**
     * Access current weather data for any location on Earth including over 200,000 cities!
     * Open Weathe Map API collect and process weather data from different sources such as global
     * and local weather models, satellites, radars and vast network of weather stations.
     *
     * Documentation : https://openweathermap.org/current.
     *
     * @param array $query
     *
     */
    private function getCurrent(array $query)
    {
        $ep = 'data/' . config('openweather.weather_api_version', '2.5') . '/weather?';

        $data = $this->makeClient()->client()->fetch($ep, $query);

        return $this->respond((new WeatherFormat())->formatCurrent($data));
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
        $ep = 'data/' . config('openweather.onecall_api_version', '2.5') . '/onecall?';
        $data = $this->makeClient()->client()->fetch($ep, $query);

        return $this->respond((new WeatherFormat())->formatOneCall($data));
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
        $ep = 'data/' . config('openweather.forecast_api_version', '2.5') . '/forecast?';
        $data = $this->makeClient()->client()->fetch($ep, $query);

        return $this->respond((new WeatherFormat())->format3Hourly($data));
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
        $ep = 'data/' . config('openweather.historical_api_version', '2.5') . '/onecall/timemachine?';
        $data = $this->makeClient()->client()->fetch($ep, $query);

        return $this->respond((new WeatherFormat())->formatHistorical($data));
    }

    /**
     * Air Pollution API concept
     * Air Pollution API provides current, forecast and historical air pollution data for any coordinates on the globe
     * Besides basic Air Quality Index, the API returns data about polluting gases, such as Carbon monoxide (CO), Nitrogen monoxide (NO),
     * Nitrogen dioxide (NO2), Ozone (O3),Sulphur dioxide (SO2), Ammonia (NH3), and particulates (PM2.5 and PM10).
     * Air pollution forecast is available for 5 days with hourly granularity.
     * When a start/end range is provided the historical /air_pollution/history
     * endpoint is used; otherwise the current /air_pollution endpoint is used.
     *
     * Documentation : https://openweathermap.org/api/air-pollution.
     *
     * @param array $query
     *
     */
    private function getAirPollution(array $query)
    {
        // Prefer the correctly spelled key, but fall back to the historical
        // misspelled `polution_api_version` so previously published configs keep working.
        $version = config('openweather.pollution_api_version')
            ?? config('openweather.polution_api_version')
            ?? '2.5';

        // The current-conditions endpoint ignores start/end and always returns "now".
        // When a time range is supplied, OpenWeatherMap serves historical data from the
        // dedicated /air_pollution/history endpoint (both start and end are required).
        $route = (! empty($query['start']) && ! empty($query['end']))
            ? 'air_pollution/history'
            : 'air_pollution';

        $ep = 'data/' . $version . '/' . $route . '?';
        $data = $this->makeClient()->client()->fetch($ep, $query);

        return $this->respond((new WeatherFormat())->formatAirPollution($data));
    }


    /**
     * Geocoding API is a simple tool that we have developed to ease the search for locations
     * while working with geographic names and coordinates.
     *
     * Documentation : https://openweathermap.org/api/geocoding-api.
     *
     * @param string $type
     * @param array $query
     *
     */
    private function getGeo(string $type, array $query)
    {
        $ep = 'geo/' . config('openweather.geo_api_version', '1.0') . '/' . $type;

        return $this->respond($this->makeClient()->client()->fetch($ep, $query));
    }
}
