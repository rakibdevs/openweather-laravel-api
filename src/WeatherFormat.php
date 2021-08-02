<?php

namespace RakibDevs\Weather;

class WeatherFormat
{
    protected $format;

    /**
     * format date based on configuration.
     *
     * @param string $timestamp, string $tz
     * @return string
     */

    public function __construct($format)
    {
        $this->format = $format;
    }

    public function dt(string $timestamp, string $tz)
    {
        return date($this->format->dt_format, ($timestamp + $tz));
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
