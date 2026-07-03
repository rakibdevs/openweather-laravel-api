<?php

namespace RakibDevs\Weather\Tests\Unit;

use RakibDevs\Weather\Tests\TestCase;
use RakibDevs\Weather\WeatherFormat;

class WeatherFormatTest extends TestCase
{
    public function test_dt_formats_unix_timestamps_using_configured_format()
    {
        $format = new WeatherFormat();

        $this->assertSame(date('m/d/Y h:i A', 1609459200), $format->dt(1609459200));
        // string timestamps are coerced safely (no PHP 8.x deprecation)
        $this->assertSame(date('m/d/Y h:i A', 1609459200), $format->dt('1609459200'));
    }

    public function test_one_call_formatting_tolerates_missing_optional_blocks()
    {
        $format = new WeatherFormat();

        $res = json_decode(json_encode([
            'timezone_offset' => 21600,
            'current' => ['dt' => 1609459200, 'sunrise' => 1609460000, 'sunset' => 1609500000],
            // no minutely / hourly / daily keys at all
        ]));

        $out = $format->formatOneCall($res);

        $this->assertSame(date('m/d/Y h:i A', 1609459200), $out->current->dt);
    }
}
