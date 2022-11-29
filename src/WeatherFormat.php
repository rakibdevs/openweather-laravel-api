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
     * @param string $timestamp, string $tz
     * @return string
     */
    public function dt(string $timestamp, int $tz)
    {
        return date($this->dateFormat, $timestamp + $tz);
    }

    public function formatCurrent($res)
    {
        $tz = $res->timezone;
        // modify date in given format
        $res->sys->sunrise = $this->dt($res->sys->sunrise, $tz);
        $res->sys->sunset = $this->dt($res->sys->sunset, $tz);
        $res->dt = $this->dt($res->dt, $tz);

        return $res;
    }


    public function formatOneCall($res)
    {
        $tz = $res->timezone_offset;

        // modify date of current data

        $res->current->sunrise = $this->dt($res->current->sunrise, $tz);
        $res->current->sunset = $this->dt($res->current->sunset, $tz);
        $res->current->dt = $this->dt($res->current->dt, $tz);

        // modify date of minutely data

        if ($res->minutely) {
            foreach ($res->minutely as $key => $val) {
                $res->minutely[$key]->dt = $this->dt($val->dt, $tz);
            }
        }

        // modify date of hourly data

        if ($res->hourly) {
            foreach ($res->hourly as $key => $val) {
                $res->hourly[$key]->dt = $this->dt($val->dt, $tz);
            }
        }

        // modify date of daily data

        if ($res->daily) {
            foreach ($res->daily as $key => $val) {
                $res->daily[$key]->dt = $this->dt($val->dt, $tz);
                $res->daily[$key]->sunrise = $this->dt($val->sunrise, $tz);
                $res->daily[$key]->sunset = $this->dt($val->sunset, $tz);
            }
        }

        return $res;
    }

    public function format3Hourly($res)
    {
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

    public function formatHistorical($res)
    {
        $tz = $res->timezone_offset;

        // Historical data response structure is different in One Call API v3.0
        // timestamps returned
        if (config('openweather.historical_api_version', '2.5') == '3.0') {
            // modify date of current data
            $res->data[0]->sunrise = $this->dt($res->data[0]->sunrise, $tz);
            $res->data[0]->sunset = $this->dt($res->data[0]->sunset, $tz);
            $res->data[0]->dt = $this->dt($res->data[0]->dt, $tz);
            return $res;
        }

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

    public function formatAirPollution($res)
    {
        foreach ($res->list as $key => $val) {
            $res->list[$key]->dt = $this->dt($val->dt, 0);
        }

        return $res;
    }
}
