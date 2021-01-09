## Laravel [Open Weather](https://openweathermap.org/) API


## Supported APIs
| Operation | English Input | |
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


Publish the required package configuration file using the artisan command:
```
	$ php artisan vendor:publish
```
Edit the `config/openweather.php` file and modify the `api_key` value with your Open Weather Map api key.
```
	return [
	    'api_key' 			=> 'ae9f7b6a0cfc2563ec1d24f3c267ad42',
	    'lang' 				=> 'en',
	    'date_format'       => 'm/d/Y',
	    'time_format'       => 'h:i A',
	    'day_format'        => 'l',
	    'temp_format'       => 'c'         // c for celcius, f for farenheit, k for kelvin
	];
```


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
{#294 ▼
  +"coord": {#296 ▼
    +"lon": 90.4074
    +"lat": 23.7104
  }
  +"weather": array:1 [▼
    0 => {#280 ▼
      +"id": 721
      +"main": "Haze"
      +"description": "haze"
      +"icon": "50d"
    }
  ]
  +"base": "stations"
  +"main": {#290 ▼
    +"temp": 26
    +"feels_like": 25.42
    +"temp_min": 26
    +"temp_max": 26
    +"pressure": 1009
    +"humidity": 57
  }
  +"visibility": 3500
  +"wind": {#284 ▼
    +"speed": 4.12
    +"deg": 280
  }
  +"clouds": {#283 ▼
    +"all": 85
  }
  +"dt": "01/09/2021 04:16 PM"
  +"sys": {#281 ▼
    +"type": 1
    +"id": 9145
    +"country": "BD"
    +"sunrise": "01/09/2021 06:42 AM"
    +"sunset": "01/09/2021 05:28 PM"
  }
  +"timezone": 21600
  +"id": 1185241
  +"name": "Dhaka"
  +"cod": 200
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



## License

Laravel Open Weather API is licensed under [The MIT License (MIT)](LICENSE).
