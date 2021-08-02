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
 

	public  function client($type = null)
	{
		$url = $type == 'geo'?$this->geo_api_url:$this->url;
        $this->service = new Client([
            'base_uri' => $this->url,
            'timeout' => 10.0,
        ]);
        return $this;
	}

	public  function fetch($route)
	{
		try {
            $response = $this->service->request('GET', $route);
            if ($response->getStatusCode() == 200) {
                return json_decode($response->getBody()->getContents());
            }
        } catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e->getMessage());
        }
	}
}