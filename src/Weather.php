<?php

namespace RakibDevs\Weather;

/**
 * Laravel OpenWeather API (openweather-laravel-api) is a Laravel package to connect Open Weather Map APIs ( https://openweathermap.org/api ) and access free API services easily.
 *
 * @package  openweather-laravel-api
 * @author   Md. Rakibul Islam <rakib1708@gmail.com>
 * @version  dev-master
 * @since    2021-01-09
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Illuminate\Support\Facades\Config;
use RakibDevs\Weather\Exceptions\WeatherException;

class Weather
{
    /**
     * base endpoint : https://api.openweathermap.org/data/2.5/.
     *
     * @var string
     */

    protected $url = 'https://api.openweathermap.org/data/2.5/';

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

    protected $client;

    /**
     * Geocoding API endpoint : http://api.openweathermap.org/geo/1.0/.
     * See documentation : https://openweathermap.org/api/geocoding-api.
     *
     * @var string
     */

    protected $geo_api_url = 'http://api.openweathermap.org/geo/1.0/';

    protected $geo_client;

    /**
     * Get a free Open Weather Map API key : https://openweathermap.org/price.
     *
     * @var string
     */

    protected $api_key;

    /**
     * temp_format available strings are c, f, k.
     *
     * @var string
     */

    protected $temp_format;

    protected $dt_format;

    protected $date_format;

    protected $time_format;

    protected $day_format;

    protected $lang;

    protected $uom;

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



    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout' => 10.0,
        ]);

        $this->geo_client = new Client([
            'base_uri' => $this->geo_api_url,
            'timeout' => 10.0,
        ]);

        $this->api_key = Config::get('openweather.api_key');
        $this->temp_format = Config::get('openweather.temp_format', 'k');
        $this->date_format = Config::get('openweather.date_format', 'm/d/Y');
        $this->time_format = Config::get('openweather.time_format', 'h:i A');
        $this->day_format = Config::get('openweather.day_format', 'l');
        $this->lang = Config::get('openweather.lang', 'en');
        $this->dt_format = $this->date_format.' '.$this->time_format;
        $this->uom = $this->units[$this->temp_format];
    }

    /**
     * format date based on configuration.
     *
     * @param string $timestamp, string $tz
     * @return string
     */

    private function dt(string $timestamp, string $tz)
    {
        return date($this->dt_format, ($timestamp + $tz));
    }

    /**
     * build query parameters.
     *
     * @param array $params
     * @return string
     */

    private function buildParams(array $params)
    {
        $params['appid'] = $this->api_key;
        $params['units'] = $this->uom;
        $params['lang'] = $this->lang;

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
        try {
            $response = $this->client->request('GET', $this->current.$this->buildParams($query));
            if ($response->getStatusCode() == 200) {
                $res = json_decode($response->getBody()->getContents());
                $tz = $res->timezone;
               
                // modify date in given format
                $res->sys->sunrise = $this->dt($res->sys->sunrise, $tz);
                $res->sys->sunset = $this->dt($res->sys->sunset, $tz);
                $res->dt = $this->dt($res->dt, $tz);
                
                return $res;
            }
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e);
        }
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
        try {
            $response = $this->client->request('GET', $this->one_call.$this->buildParams($query));

            if ($response->getStatusCode() == 200) {
                $res = json_decode($response->getBody()->getContents());
                $tz = $res->timezone_offset;

                // modify date of current data

                $res->current->sunrise = $this->dt($res->current->sunrise, $tz);
                $res->current->sunset = $this->dt($res->current->sunset, $tz);
                $res->current->dt = $this->dt($res->current->dt, $tz);

                // modify date of minutely data

                foreach ($res->minutely as $key => $val) {
                    $res->minutely[$key]->dt = $this->dt($val->dt, $tz);
                }

                // modify date of hourly data

                foreach ($res->hourly as $key => $val) {
                    $res->hourly[$key]->dt = $this->dt($val->dt, $tz);
                }

                // modify date of daily data

                foreach ($res->daily as $key => $val) {
                    $res->daily[$key]->dt = $this->dt($val->dt, $tz);
                    $res->daily[$key]->sunrise = $this->dt($val->sunrise, $tz);
                    $res->daily[$key]->sunset = $this->dt($val->sunset, $tz);
                }
                
                return $res;
            }
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e);
        }
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
        try {
            $response = $this->client->request('GET', $this->forecast.$this->buildParams($query));
            if ($response->getStatusCode() == 200) {
                $res = json_decode($response->getBody()->getContents());
                $tz = $res->city->timezone;
               
                // modify date in given format
                $res->city->sunrise = $this->dt($res->city->sunrise, $tz);
                $res->city->sunset = $this->dt($res->city->sunset, $tz);

                // modify date of list data

                foreach ($res->list as $key => $val) {
                    $res->list[$key]->dt = $this->dt($val->dt, $tz);
                }
                
                return $res;
            }
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e);
        }
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
        try {
            $response = $this->client->request('GET', $this->historical.$this->buildParams($query));


            if ($response->getStatusCode() == 200) {
                $res = json_decode($response->getBody()->getContents());

                $tz = $res->timezone_offset;
               
                // modify date of current data

                $res->current->sunrise = $this->dt($res->current->sunrise, $tz);
                $res->current->sunset = $this->dt($res->current->sunset, $tz);
                $res->current->dt = $this->dt($res->current->dt, $tz);

                // modify date of hourly data

                foreach ($res->hourly as $key => $val) {
                    $res->hourly[$key]->dt = $this->dt($val->dt, $tz);
                }
                
                return $res;
            }
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e);
        }
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
        try {
            $response = $this->client->request('GET', $this->air_pollution.$this->buildParams($query));

            if ($response->getStatusCode() == 200) {
                $res = json_decode($response->getBody()->getContents());

                // modify date of list data

                foreach ($res->list as $key => $val) {
                    $res->list[$key]->dt = $this->dt($val->dt, 0);
                }
                
                return $res;
            }
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e);
        }
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
        try {
            $response = $this->geo_client->request('GET', $type.$this->buildParams($query));

            if ($response->getStatusCode() == 200) {
                $res = json_decode($response->getBody()->getContents());

                return $res;
                // modify date of list data

                foreach ($res->list as $key => $val) {
                    $res->list[$key]->dt = $this->dt($val->dt, 0);
                }
                
                return $res;
            }
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e);
        }
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
