<?php

namespace RakibDevs\Weather\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Orchestra\Testbench\TestCase as BaseTestCase;
use RakibDevs\Weather\Weather;
use RakibDevs\Weather\WeatherServiceProvider;

abstract class TestCase extends BaseTestCase
{
    /**
     * Requests captured by the mocked Guzzle client during a test.
     *
     * @var array
     */
    protected $history = [];

    protected function getPackageProviders($app)
    {
        return [WeatherServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Weather' => \RakibDevs\Weather\Facades\Weather::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        $app['config']->set('openweather.api_key', 'test-api-key');
    }

    /**
     * Load a JSON fixture as a raw string.
     */
    protected function fixture(string $name): string
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $name);
    }

    /**
     * Build a Weather instance backed by a mocked Guzzle client that returns the
     * given queued responses. Outbound requests are recorded in $this->history.
     *
     * @param array $responses Array of GuzzleHttp\Psr7\Response
     */
    protected function fakeWeather(array $responses): Weather
    {
        $this->history = [];
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($this->history));

        return new Weather(new Client(['handler' => $stack]));
    }

    /**
     * Convenience helper to build a 200 JSON response from a fixture file.
     */
    protected function jsonResponse(string $fixture, int $status = 200): Response
    {
        return new Response($status, ['Content-Type' => 'application/json'], $this->fixture($fixture));
    }

    /**
     * Return the query parameters of the last captured request.
     */
    protected function lastQuery(): array
    {
        $request = end($this->history)['request'];
        parse_str($request->getUri()->getQuery(), $query);

        return $query;
    }

    /**
     * Return the path (with query) of the last captured request.
     */
    protected function lastUri(): string
    {
        $uri = end($this->history)['request']->getUri();

        return $uri->getPath() . '?' . $uri->getQuery();
    }
}
