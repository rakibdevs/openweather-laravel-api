<?php

namespace RakibDevs\Weather;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use RakibDevs\Weather\Exceptions\WeatherException;

class WeatherClient
{
    /**
     * Get a free Open Weather Map API key : https://openweathermap.org/price.
     *
     * @var string
     */

    protected $api_key;

    /**
     * base endpoint : https://api.openweathermap.org/data/2.5/.
     *
     * @var string
     */

    protected $url = 'https://api.openweathermap.org/data/2.5/';

    /**
     * Geocoding API endpoint : http://api.openweathermap.org/geo/1.0/.
     * See documentation : https://openweathermap.org/api/geocoding-api.
     *
     * @var string
     */

    protected $geo_api_url = 'http://api.openweathermap.org/geo/1.0/';


    protected $service;

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
        $this->format->dt_format = $this->format->date_format . ' ' . $this->format->time_format;
        $this->uom = $this->units[$this->format->temp_format];
    }

    /**
     * build query parameters.
     *
     * @param array $params
     * @return string
     */

    private function buildQueryString(array $params)
    {
        $params['appid'] = $this->api_key;
        $params['units'] = $this->uom;
        $params['lang'] = $this->format->lang;

        return http_build_query($params);
    }


    public function client($type = null)
    {
        $url = $type == 'geo' ? $this->geo_api_url : $this->url;
        $this->service = new Client([
            'base_uri' => $url,
            'timeout' => 10.0,
        ]);

        return $this;
    }

    public function fetch($route, $params = [])
    {
        try {
            $route = $route . $this->buildQueryString($params);
            $response = $this->service->request('GET', $route);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->getContents());
            }
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e->getMessage());
        }
    }
}
