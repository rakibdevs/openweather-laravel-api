<?php

namespace RakibDevs\Weather;

use RakibDevs\Weather\Src\Exceptions\WeatherException;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\TooManyRedirectsException;

class Weather
{

    protected $url = 'https://api.openweathermap.org/data/2.5/';

    protected $client;

    protected $api_key;

    protected $temp_format; 

    protected $date_time_format;

    protected $date_format;

    protected $time_format;

    protected $day_format;


    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 10.0,
        ]);
        $this->api_key     = Config::get('openweather.api_key');
        $this->temp_format = Config::get('openweather.temp_format','k');
        $this->date_format = Config::get('openweather.date_format','m/d/Y');
        $this->time_format = Config::get('openweather.time_format','h:i A');
        $this->day_format  = Config::get('openweather.day_format','l');
        $this->date_time_format  = $this->date_format.' '.$this->time_format;
    }

    private function tempConvert($num, $type = null)
    {
        $type = $type??$this->temp_format;
        if($type == 'c')
            return round($num - 273.15, 2);
        else if($type == 'f')
            return round((($num - 273.15) * 9/5 + 32),2);
        else
            return $num;
    }

    private function getCurrent($query)
    {
        try{
            $response = $this->client->request('GET', 'weather?'.$query.'&appid='.$this->api_key);
            if($response->getStatusCode() == 200){
                $result = json_decode($response->getBody()->getContents());
                // modify data based on user requirement

                // modify temparature in [C,F,K]
                $result->main->temp       = $this->tempConvert($result->main->temp);
                $result->main->feels_like = $this->tempConvert($result->main->feels_like);
                $result->main->temp_min = $this->tempConvert($result->main->temp_min);
                $result->main->temp_max = $this->tempConvert($result->main->temp_max);
                $result->main->temp_max = $this->tempConvert($result->main->temp_max);

                // modify date in given format
                $result->sys->sunrise = date($this->date_time_format, $result->sys->sunrise+$result->timezone);
                $result->sys->sunset = date($this->date_time_format, $result->sys->sunset+$result->timezone);
                
                return $result;
            }
        } 
        catch (ClientException | RequestException | ConnectException | ServerException | TooManyRedirectsException $e) {
            throw new WeatherException($e->getMessage());
        }
    }

    public function getCurrentByCity($city)
    {
        return $this->getCurrent('q='.$city);
    }

    public function getTemperature($city)
    {

    }

    public function getCurrentByCoordinates($lat, $lon)
    {
        return $this->getCurrent($lat.','.$lon, $date);
    }

}
