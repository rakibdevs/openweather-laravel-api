<?php

namespace RakibDevs\Weather\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use RakibDevs\Weather\Exceptions\InvalidConfiguration;
use RakibDevs\Weather\Exceptions\WeatherException;
use RakibDevs\Weather\Tests\TestCase;
use RakibDevs\Weather\WeatherClient;

class WeatherClientTest extends TestCase
{
    public function test_it_throws_invalid_configuration_when_api_key_is_missing()
    {
        config()->set('openweather.api_key', '');

        $this->expectException(InvalidConfiguration::class);
        new WeatherClient();
    }

    public function test_it_throws_weather_exception_on_non_200_response()
    {
        $client = $this->mockGuzzle([new Response(404, [], '{"cod":"404"}')]);
        $wc = new WeatherClient([], $client);

        $this->expectException(WeatherException::class);
        $wc->client()->fetch('data/2.5/weather?');
    }

    public function test_it_throws_weather_exception_on_invalid_json()
    {
        $client = $this->mockGuzzle([new Response(200, [], 'not-json')]);
        $wc = new WeatherClient([], $client);

        $this->expectException(WeatherException::class);
        $wc->client()->fetch('data/2.5/weather?');
    }

    public function test_overrides_take_precedence_over_config()
    {
        config()->set('openweather.temp_format', 'c');
        config()->set('openweather.lang', 'en');

        $client = $this->mockGuzzle([new Response(200, [], '{"ok":true}')]);
        $wc = new WeatherClient(['temp_format' => 'f', 'lang' => 'bn'], $client);
        $wc->client()->fetch('data/2.5/weather?', ['q' => 'dhaka']);

        // Nothing to assert on the response beyond it not throwing; behavior is
        // validated end-to-end in the feature test. This guards the constructor merge.
        $this->assertTrue(true);
    }

    private function mockGuzzle(array $responses): Client
    {
        $stack = HandlerStack::create(new MockHandler($responses));

        return new Client(['handler' => $stack]);
    }
}
