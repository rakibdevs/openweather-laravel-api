<?php

namespace RakibDevs\Weather;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Illuminate\Support\Facades\Cache;
use RakibDevs\Weather\Exceptions\InvalidConfiguration;
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

    protected $url = 'https://api.openweathermap.org/';

    /**
     * @var \GuzzleHttp\Client|null
     */
    protected $service;

    /**
     * Units: available units are c, f, k.
     *
     * For temperature in Fahrenheit (f) and wind speed in miles/hour, use units=imperial
     * For temperature in Celsius (c) and wind speed in meter/sec, use units=metric\
     *
     * @var array
     */
    protected $units = [
        'c' => 'metric',
        'f' => 'imperial',
        'k' => 'standard',
    ];

    /**
     * @var array
     */
    protected $config;

    /**
     * @param array                   $overrides  Per-call config overrides (e.g. temp_format, lang).
     * @param \GuzzleHttp\Client|null $httpClient Optional Guzzle client for dependency injection / testing.
     */
    public function __construct(array $overrides = [], ?Client $httpClient = null)
    {
        $this->setConfigParameters($overrides);
        $this->setApi();
        $this->service = $httpClient;
    }

    protected function setApi()
    {
        $this->api_key = $this->config['api_key'] ?? '';
        if ($this->api_key == '') {
            throw new InvalidConfiguration();
        }
    }


    protected function setConfigParameters(array $overrides = [])
    {
        $this->config = array_merge((array) config('openweather'), $overrides);
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
        $params['units'] = $this->units[$this->config['temp_format']];
        $params['lang'] = $this->config['lang'];

        return http_build_query($params);
    }


    public function client()
    {
        if (! $this->service instanceof Client) {
            $this->service = new Client([
                'base_uri' => $this->url,
                'timeout' => 10.0,
            ]);
        }

        return $this;
    }

    public function fetch($route, $params = [])
    {
        $route = $route . $this->buildQueryString($params);

        if (! empty($this->config['cache_enabled'])) {
            $ttl = (int) ($this->config['cache_ttl'] ?? 600);

            return Cache::remember('openweather.' . md5($route), $ttl, function () use ($route) {
                return $this->request($route);
            });
        }

        return $this->request($route);
    }

    /**
     * Execute the HTTP request and decode the JSON response.
     *
     * @param string $route
     * @return object
     *
     * @throws \RakibDevs\Weather\Exceptions\WeatherException
     */
    private function request(string $route)
    {
        try {
            $response = $this->client()->service->request('GET', $route);
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e->getMessage());
        }

        if ($response->getStatusCode() !== 200) {
            throw new WeatherException('OpenWeatherMap API returned HTTP status ' . $response->getStatusCode());
        }

        $data = json_decode($response->getBody()->getContents());

        if (json_last_error() !== JSON_ERROR_NONE || $data === null) {
            throw new WeatherException('Unable to decode the OpenWeatherMap API response.');
        }

        return $data;
    }
}
