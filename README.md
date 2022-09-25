## Laravel [Open Weather](https://openweathermap.org/) API

![Packagist](https://img.shields.io/packagist/dt/rakibdevs/openweather-laravel-api)
[![GitHub stars](https://img.shields.io/github/stars/rakibdevs/openweather-laravel-api)](https://github.com/rakibdevs/openweather-laravel-api/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/rakibdevs/openweather-laravel-api)](https://github.com/rakibdevs/openweather-laravel-api/network)
[![GitHub issues](https://img.shields.io/github/issues/rakibdevs/openweather-laravel-api)](https://github.com/rakibdevs/openweather-laravel-api/issues)
[![GitHub license](https://img.shields.io/github/license/rakibdevs/openweather-laravel-api)](https://github.com/rakibdevs/openweather-laravel-api/blob/master/LICENSE)

 Laravel OpenWeather API (openweather-laravel-api) is a Laravel package to connect Open Weather Map APIs ( https://openweathermap.org/api ) and access free API services (current weather, weather forecast, weather history) easily.

## Supported APIs
| APIs | Get data by | 
| --- | --- |
| [Current Weather](https://openweathermap.org/current) | By city name, city ID, geographic coordinates, ZIP code |
| [One Call API](https://openweathermap.org/api/one-call-api) | By geographic coordinates|
| [4 Day 3 Hour Forecast](https://openweathermap.org/forecast5) | By city name, city ID, geographic coordinates, ZIP code |
| [5 Day Historical](https://openweathermap.org/api/one-call-api#history) | By geographic coordinates |
| [Air Pollution](https://openweathermap.org/api/air-pollution) | By geographic coordinates |
| [Geocoding API](https://openweathermap.org/api/geocoding-api) | By geographic coordinates |


## Installation

Install the package through [Composer](http://getcomposer.org).
On the command line:

```
composer require rakibdevs/openweather-laravel-api

```


## Configuration 
If Laravel > 7, no need to add provider

Add the following to your `providers` array in `config/app.php`:

```php
'providers' => [
    // ...
    RakibDevs\Weather\WeatherServiceProvider::class,
],
'aliases' => [
    //...
    'Weather' => RakibDevs\Weather\Weather::class,	
];


```
Add API key and desired language in `.env`
```
OPENWAETHER_API_KEY=
OPENWAETHER_API_LANG=en
```

Publish the required package configuration file using the artisan command:
```
	$ php artisan vendor:publish
```
Edit the `config/openweather.php` file and modify the `api_key` value with your Open Weather Map api key.
```php
	return [
	    'api_key' 	        => env('OPENWAETHER_API_KEY', ''),
    	    'onecall_api_version' => '2.5',
            'historical_api_version' => '2.5',
            'forecast_api_version' => '2.5',
            'polution_api_version' => '2.5',
            'geo_api_version' => '1.0',
	    'lang' 		=> env('OPENWAETHER_API_LANG', 'en'),
	    'date_format'       => 'm/d/Y',
	    'time_format'       => 'h:i A',
	    'day_format'        => 'l',
	    'temp_format'       => 'c'         // c for celcius, f for farenheit, k for kelvin
	];
```

Now you can configure API version from config as [One Call API](https://openweathermap.org/price) is upgraded to version 3.0. Please set available api version in config. 


## Usage
Here you can see some example of just how simple this package is to use.

```php
use RakibDevs\Weather\Weather;

$wt = new Weather();

$info = $wt->getCurrentByCity('dhaka');    // Get current weather by city name


```

### [Current weather](https://openweathermap.org/current) 
Access current weather data for any location on Earth including over 200,000 cities! [OpenWeather](https://openweathermap.org/) collect and process weather data from different sources such as global and local weather models, satellites, radars and vast network of weather stations

```php

// By city name
$info = $wt->getCurrentByCity('dhaka'); 

// By city ID - download list of city id here http://bulk.openweathermap.org/sample/
$info = $wt->getCurrentByCity(1185241); 

// By Zip Code - string with country code 
$info = $wt->getCurrentByZip('94040,us');  // If no country code specified, us will be default

// By coordinates : latitude and longitude
$info = $wt->getCurrentByCord(23.7104, 90.4074);

```

#### Output:
```
{
  "coord": {
    "lon": 90.4074
    "lat": 23.7104
  }
  "weather":[
    0 => { 
      "id": 721
      "main": "Haze"
      "description": "haze"
      "icon": "50d"
    }
  ]
  "base": "stations"
  "main": {
    "temp": 26
    "feels_like": 25.42
    "temp_min": 26
    "temp_max": 26
    "pressure": 1009
    "humidity": 57
  }
  "visibility": 3500
  "wind": {
    "speed": 4.12
    "deg": 280
  }
  "clouds": {
    "all": 85
  }
  "dt": "01/09/2021 04:16 PM"
  "sys": {
    "type": 1
    "id": 9145
    "country": "BD"
    "sunrise": "01/09/2021 06:42 AM"
    "sunset": "01/09/2021 05:28 PM"
  }
  "timezone": 21600
  "id": 1185241
  "name": "Dhaka"
  "cod": 200
}

```

### [One Call API](https://openweathermap.org/api/one-call-api) 
Make just one API call and get all your essential weather data for a specific location with OpenWeather One Call API.

```php
// By coordinates : latitude and longitude
$info = $wt->getOneCallByCord(23.7104, 90.4074);

```

### [4 Day 3 Hour Forecast](https://openweathermap.org/forecast5) 
4 day forecast is available at any location or city. It includes weather forecast data with 3-hour step.

```php
// By city name
$info = $wt->get3HourlyByCity('dhaka'); 

// By city ID - download list of city id here http://bulk.openweathermap.org/sample/
$info = $wt->get3HourlyByCity(1185241); 

// By Zip Code - string with country code 
$info = $wt->get3HourlyByZip('94040,us');  // If no country code specified, us will be default

// By coordinates : latitude and longitude
$info = $wt->get3HourlyByCord(23.7104, 90.4074);

```

### [5 Day Historical](https://openweathermap.org/api/one-call-api#history) 
Get access to historical weather data for the previous 5 days.

```php

// By coordinates : latitude, longitude and date
$info = $wt->getHistoryByCord(23.7104, 90.4074, '2020-01-09');

```

### [Air Pollution](https://openweathermap.org/api/one-call-api#history) 
Air Pollution API provides current, forecast and historical air pollution data for any coordinates on the globe

Besides basic Air Quality Index, the API returns data about polluting gases, such as Carbon monoxide (CO), Nitrogen monoxide (NO), Nitrogen dioxide (NO2), Ozone (O3), Sulphur dioxide (SO2), Ammonia (NH3), and particulates (PM2.5 and PM10).

Air pollution forecast is available for 5 days with hourly granularity. Historical data is accessible from 27th November 2020.

```php

// By coordinates : latitude, longitude and date
$info = $wt->getAirPollutionByCord(23.7104, 90.4074);

```

### [Geocoding API](https://openweathermap.org/api/one-call-api#history) 
Geocoding API is a simple tool that we have developed to ease the search for locations while working with geographic names and coordinates.
-> Direct geocoding converts the specified name of a location or area into the exact geographical coordinates;
-> Reverse geocoding converts the geographical coordinates into the names of the nearby locations.

```php
// By city name
$info = $wt->getGeoByCity('dhaka');

// By coordinates : latitude, longitude and date
$info = $wt->getGeoByCity(23.7104, 90.4074);

```

### [Free API Limitations](https://openweathermap.org/api/one-call-api#history) 
- 60 calls/minute 
- 1,000,000 calls/month
- 1000 calls/day when using Onecall requests



## License

Laravel Open Weather API is licensed under [The MIT License (MIT)](LICENSE).
