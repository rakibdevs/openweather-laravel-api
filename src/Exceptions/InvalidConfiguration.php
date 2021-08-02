<?php
namespace RakibDevs\Weather\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function apiKeyNotSpecified()
    {
        return new static('There was no `api_key` specified. You must provide a valid API KEY to get weather data from Open Weather.');
    }
}
