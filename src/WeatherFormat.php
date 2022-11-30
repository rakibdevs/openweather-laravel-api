<?php

namespace RakibDevs\Weather;

class WeatherFormat
{
    protected $dateFormat;

    public function __construct()
    {
        $format = config('openweather');
        $this->dateFormat = $format['date_format'] . ' ' . $format['time_format'];
    }

    /**
     * format date based on configuration.
     *
     * @param string $timestamp, int $tz
     * @return string
     */
    public function dt(string $timestamp)
    {
        return date($this->dateFormat, $timestamp);
    }

    public function formatCurrent($res)
    {
        // modify date in given format
        $res->sys->sunrise = date($this->dateFormat, $res->sys->sunrise);
        $res->sys->sunset = date($this->dateFormat, $res->sys->sunset);
        $res->dt = date($this->dateFormat, $res->dt);

        return $res;
    }


    public function formatOneCall($res)
    {
        $tz = $res->timezone_offset;

        // modify date of current data

        $res->current->sunrise = date($this->dateFormat, $res->current->sunrise);
        $res->current->sunset = date($this->dateFormat, $res->current->sunset);
        $res->current->dt = date($this->dateFormat, $res->current->dt);

        // modify date of minutely data

        if ($res->minutely) {
            foreach ($res->minutely as $key => $val) {
                $res->minutely[$key]->dt = date($this->dateFormat, $val->dt);
            }
        }

        // modify date of hourly data

        if ($res->hourly) {
            foreach ($res->hourly as $key => $val) {
                $res->hourly[$key]->dt = date($this->dateFormat, $val->dt);
            }
        }

        // modify date of daily data

        if ($res->daily) {
            foreach ($res->daily as $key => $val) {
                $res->daily[$key]->dt = date($this->dateFormat, $val->dt);
                $res->daily[$key]->sunrise = date($this->dateFormat, $val->sunrise);
                $res->daily[$key]->sunset = date($this->dateFormat, $val->sunset);
            }
        }

        return $res;
    }

    public function format3Hourly($res)
    {
        $tz = $res->city->timezone;

        // modify date in given format
        $res->city->sunrise = date($this->dateFormat, $res->city->sunrise);
        $res->city->sunset = date($this->dateFormat, $res->city->sunset);

        // modify date of list data

        foreach ($res->list as $key => $val) {
            $res->list[$key]->dt = date($this->dateFormat, $val->dt);
        }

        return $res;
    }

    public function formatHistorical($res)
    {
        $tz = $res->timezone_offset;

        // Historical data response structure is different in One Call API v3.0
        // timestamps returned
        if (config('openweather.historical_api_version', '2.5') == '3.0') {
            // modify date of current data
            $res->data[0]->sunrise = date($this->dateFormat, $res->data[0]->sunrise);
            $res->data[0]->sunset = date($this->dateFormat, $res->data[0]->sunset);
            $res->data[0]->dt = date($this->dateFormat, $res->data[0]->dt);
            return $res;
        }

        // modify date of current data
        $res->current->sunrise = date($this->dateFormat, $res->current->sunrise);
        $res->current->sunset = date($this->dateFormat, $res->current->sunset);
        $res->current->dt = date($this->dateFormat, $res->current->dt);

        // modify date of hourly data
        foreach ($res->hourly as $key => $val) {
            $res->hourly[$key]->dt = date($this->dateFormat, $val->dt);
        }

        return $res;
    }

    public function formatAirPollution($res)
    {
        foreach ($res->list as $key => $val) {
            $res->list[$key]->dt = date($this->dateFormat, $val->dt);
        }

        return $res;
    }
}
