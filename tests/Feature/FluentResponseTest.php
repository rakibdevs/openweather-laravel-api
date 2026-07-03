<?php

namespace RakibDevs\Weather\Tests\Feature;

use Illuminate\Support\Collection;
use RakibDevs\Weather\Tests\TestCase;
use RakibDevs\Weather\WeatherResponse;

class FluentResponseTest extends TestCase
{
    public function test_default_response_is_still_stdclass()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $res = $wt->getCurrentByCity('dhaka');

        $this->assertInstanceOf(\stdClass::class, $res);
        $this->assertNotInstanceOf(WeatherResponse::class, $res);
    }

    public function test_fluent_returns_a_weather_response_wrapper()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $res = $wt->fluent()->getCurrentByCity('dhaka');

        $this->assertInstanceOf(WeatherResponse::class, $res);
    }

    public function test_property_access_is_backward_compatible()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $res = $wt->fluent()->getCurrentByCity('dhaka');

        // exactly how existing users read the stdClass response
        $this->assertSame(25.99, $res->main->temp);
        $this->assertSame('Dhaka', $res->name);
        $this->assertTrue(isset($res->main));
        $this->assertNull($res->does_not_exist);
    }

    public function test_dot_notation_and_helpers()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $res = $wt->fluent()->getCurrentByCity('dhaka');

        $this->assertSame(25.99, $res->get('main.temp'));
        $this->assertSame('haze', $res->get('weather.0.description'));
        $this->assertSame('fallback', $res->get('missing.key', 'fallback'));
        $this->assertTrue($res->has('main.humidity'));
        $this->assertFalse($res->has('main.nope'));

        // convenience accessors
        $this->assertSame(25.99, $res->temperature());
        $this->assertSame(60, $res->humidity());
        $this->assertSame('haze', $res->description());
        $this->assertSame('Haze', $res->condition());
        $this->assertSame('Dhaka', $res->city());
        $this->assertSame('BD', $res->country());
        $this->assertSame('https://openweathermap.org/img/wn/50d@2x.png', $res->iconUrl());
    }

    public function test_array_access_and_conversions()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $res = $wt->fluent()->getCurrentByCity('dhaka');

        // ArrayAccess (dot notation supported)
        $this->assertSame(25.99, $res['main.temp']);
        $this->assertTrue(isset($res['name']));

        $this->assertIsArray($res->toArray());
        $this->assertSame(25.99, $res->toArray()['main']['temp']);
        $this->assertJson($res->toJson());
        $this->assertJson((string) $res);

        // raw() returns the untouched decoded object
        $this->assertInstanceOf(\stdClass::class, $res->raw());
    }

    public function test_collect_on_a_list_response()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('forecast.json')]);

        $res = $wt->fluent()->get3HourlyByCity('dhaka');

        $list = $res->collect('list');
        $this->assertInstanceOf(Collection::class, $list);
        $this->assertCount(2, $list);
        $this->assertSame(24.5, $list->first()['main']['temp']);
    }

    public function test_fluent_flag_resets_after_a_single_call()
    {
        $wt = $this->fakeWeather([
            $this->jsonResponse('current.json'),
            $this->jsonResponse('current.json'),
        ]);

        $first = $wt->fluent()->getCurrentByCity('dhaka');
        $second = $wt->getCurrentByCity('dhaka');

        $this->assertInstanceOf(WeatherResponse::class, $first);
        $this->assertInstanceOf(\stdClass::class, $second);
    }

    public function test_fluent_composes_with_units_and_lang()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $res = $wt->fluent()->units('f')->lang('bn')->getCurrentByCity('dhaka');

        $query = $this->lastQuery();
        $this->assertSame('imperial', $query['units']);
        $this->assertSame('bn', $query['lang']);
        $this->assertInstanceOf(WeatherResponse::class, $res);
    }
}
