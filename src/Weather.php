<?php

namespace RakibDevs\Weather;

/**
 * Laravel OpenWeather API (openweather-laravel-api) is a Laravel package to connect Open Weather Map APIs ( https://openweathermap.org/api ) and access free API services easily.
 *
 * @package  openweather-laravel-api
 * @author   Md. Rakibul Islam <rakib1708@gmail.com>
 * @since    2021-01-09
 */

class Weather
{
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
            'start' => $start != null ? strtotime($start) : $start,
            'end' => $end != null ? strtotime($end) : $end,
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

        $data = (new WeatherClient)->client()->fetch($ep, $query);

        return (new WeatherFormat())->formatCurrent($data);
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
        $data = (new WeatherClient)->client()->fetch($ep, $query);

        return (new WeatherFormat())->formatOneCall($data);
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
        $data = (new WeatherClient)->client()->fetch($ep, $query);

        return (new WeatherFormat())->format3Hourly($data);
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
        $data = (new WeatherClient)->client()->fetch($ep, $query);

        return (new WeatherFormat())->formatHistorical($data);
    }

    /**
     * Air Pollution API concept
     * Air Pollution API provides current, forecast and historical air pollution data for any coordinates on the globe
     * Besides basic Air Quality Index, the API returns data about polluting gases, such as Carbon monoxide (CO), Nitrogen monoxide (NO),
     * Nitrogen dioxide (NO2), Ozone (O3),Sulphur dioxide (SO2), Ammonia (NH3), and particulates (PM2.5 and PM10).
     * Air pollution forecast is available for 5 days with hourly granularity.
     *
     * Documentation : https://openweathermap.org/api/air-pollution.
     *
     * @param array $query
     *
     */
    private function getAirPollution(array $query)
    {
        $ep = 'data/' . config('openweather.pollution_api_version', '2.5') . '/air_pollution?';
        $data = (new WeatherClient)->client()->fetch($ep, $query);

        return (new WeatherFormat())->formatAirPollution($data);
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

        return (new WeatherClient)->client()->fetch($ep, $query);
    }
}
