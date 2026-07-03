<?php

namespace RakibDevs\Weather\Tests\Feature;

use RakibDevs\Weather\Exceptions\WeatherException;
use RakibDevs\Weather\Tests\TestCase;

class WeatherApiTest extends TestCase
{
    public function test_it_gets_current_weather_by_city_name()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $res = $wt->getCurrentByCity('dhaka');

        $query = $this->lastQuery();
        $this->assertSame('dhaka', $query['q']);
        $this->assertSame('test-api-key', $query['appid']);
        $this->assertSame('metric', $query['units']); // temp_format 'c'
        $this->assertSame('en', $query['lang']);
        $this->assertStringContainsString('data/2.5/weather', $this->lastUri());

        // dates are formatted, not raw timestamps
        $this->assertSame(date('m/d/Y h:i A', 1609459200), $res->dt);
        $this->assertSame(date('m/d/Y h:i A', 1609460000), $res->sys->sunrise);
    }

    public function test_it_gets_current_weather_by_numeric_city_id()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $wt->getCurrentByCity('1185241');

        $query = $this->lastQuery();
        $this->assertSame('1185241', $query['id']);
        $this->assertArrayNotHasKey('q', $query);
    }

    public function test_it_gets_current_temp_by_city()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $main = $wt->getCurrentTempByCity('dhaka');

        $this->assertSame(25.99, $main->temp);
    }

    public function test_it_gets_current_weather_by_coordinates()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $wt->getCurrentByCord('23.71', '90.41');

        $query = $this->lastQuery();
        $this->assertSame('23.71', $query['lat']);
        $this->assertSame('90.41', $query['lon']);
    }

    public function test_it_gets_current_weather_by_zip()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json')]);

        $wt->getCurrentByZip('1207', 'bd');

        $query = $this->lastQuery();
        $this->assertSame('1207', $query['zip']);
        $this->assertSame('bd', $query['country']);
    }

    public function test_it_gets_one_call_data_and_formats_all_timestamps()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('onecall.json')]);

        $res = $wt->getOneCallByCord('23.71', '90.41');

        $this->assertStringContainsString('data/2.5/onecall', $this->lastUri());
        $this->assertSame(date('m/d/Y h:i A', 1609459200), $res->current->dt);
        $this->assertSame(date('m/d/Y h:i A', 1609459260), $res->minutely[0]->dt);
        $this->assertSame(date('m/d/Y h:i A', 1609462800), $res->hourly[0]->dt);
        $this->assertSame(date('m/d/Y h:i A', 1609480800), $res->daily[0]->dt);
        $this->assertSame(date('m/d/Y h:i A', 1609460000), $res->daily[0]->sunrise);
    }

    public function test_it_gets_3_hourly_forecast()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('forecast.json')]);

        $res = $wt->get3HourlyByCity('dhaka');

        $this->assertStringContainsString('data/2.5/forecast', $this->lastUri());
        $this->assertSame(date('m/d/Y h:i A', 1609462800), $res->list[0]->dt);
        $this->assertSame(date('m/d/Y h:i A', 1609460000), $res->city->sunrise);
    }

    public function test_it_gets_historical_data_v25()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('historical_v25.json')]);

        $res = $wt->getHistoryByCord('23.71', '90.41', '2021-01-01');

        $this->assertStringContainsString('data/2.5/onecall/timemachine', $this->lastUri());
        $this->assertArrayHasKey('dt', $this->lastQuery());
        $this->assertSame(date('m/d/Y h:i A', 1609459200), $res->current->dt);
        $this->assertSame(date('m/d/Y h:i A', 1609462800), $res->hourly[0]->dt);
    }

    public function test_it_gets_historical_data_v30_structure()
    {
        config()->set('openweather.historical_api_version', '3.0');
        $wt = $this->fakeWeather([$this->jsonResponse('historical_v30.json')]);

        $res = $wt->getHistoryByCord('23.71', '90.41', '2021-01-01');

        $this->assertStringContainsString('data/3.0/onecall/timemachine', $this->lastUri());
        $this->assertSame(date('m/d/Y h:i A', 1609459200), $res->data[0]->dt);
        $this->assertSame(date('m/d/Y h:i A', 1609460000), $res->data[0]->sunrise);
    }

    public function test_air_pollution_with_a_date_range_hits_the_history_endpoint()
    {
        config()->set('openweather.pollution_api_version', '2.5');
        $wt = $this->fakeWeather([$this->jsonResponse('air_pollution.json')]);

        $res = $wt->getAirPollutionByCord('23.71', '90.41', '2021-01-01', '2021-01-02');

        $query = $this->lastQuery();
        $this->assertStringContainsString('data/2.5/air_pollution/history', $this->lastUri());
        $this->assertSame((string) strtotime('2021-01-01'), $query['start']);
        $this->assertSame((string) strtotime('2021-01-02'), $query['end']);
        $this->assertSame(date('m/d/Y h:i A', 1609459200), $res->list[0]->dt);
    }

    public function test_air_pollution_without_dates_hits_the_current_endpoint()
    {
        config()->set('openweather.pollution_api_version', '2.5');
        $wt = $this->fakeWeather([$this->jsonResponse('air_pollution.json')]);

        $wt->getAirPollutionByCord('23.71', '90.41');

        $query = $this->lastQuery();
        $this->assertStringContainsString('data/2.5/air_pollution?', $this->lastUri());
        $this->assertStringNotContainsString('history', $this->lastUri());
        $this->assertArrayNotHasKey('start', $query);
        $this->assertArrayNotHasKey('end', $query);
    }

    public function test_air_pollution_falls_back_to_legacy_misspelled_config_key()
    {
        // Simulate a previously published config that only has the misspelled key.
        config()->set('openweather.pollution_api_version', null);
        config()->set('openweather.polution_api_version', '2.5');
        $wt = $this->fakeWeather([$this->jsonResponse('air_pollution.json')]);

        $wt->getAirPollutionByCord('23.71', '90.41');

        $this->assertStringContainsString('data/2.5/air_pollution', $this->lastUri());
    }

    public function test_it_gets_geocoding_direct_and_reverse()
    {
        $wt = $this->fakeWeather([
            $this->jsonResponse('geo_direct.json'),
            $this->jsonResponse('geo_direct.json'),
        ]);

        $direct = $wt->getGeoByCity('dhaka', '5');
        $this->assertStringContainsString('geo/1.0/direct', $this->lastUri());
        $this->assertSame('5', $this->lastQuery()['limit']);
        $this->assertSame('Dhaka', $direct[0]->name);

        $wt->getGeoByCord('23.71', '90.41');
        $this->assertStringContainsString('geo/1.0/reverse', $this->lastUri());
    }

    public function test_per_call_units_and_lang_overrides_are_applied_then_reset()
    {
        $wt = $this->fakeWeather([
            $this->jsonResponse('current.json'),
            $this->jsonResponse('current.json'),
        ]);

        $wt->units('f')->lang('bn')->getCurrentByCity('dhaka');
        $query = $this->lastQuery();
        $this->assertSame('imperial', $query['units']);
        $this->assertSame('bn', $query['lang']);

        // Overrides apply to a single request only.
        $wt->getCurrentByCity('dhaka');
        $query = $this->lastQuery();
        $this->assertSame('metric', $query['units']);
        $this->assertSame('en', $query['lang']);
    }

    public function test_it_throws_weather_exception_on_server_error()
    {
        $wt = $this->fakeWeather([$this->jsonResponse('current.json', 500)]);

        $this->expectException(WeatherException::class);
        $wt->getCurrentByCity('dhaka');
    }

    public function test_the_facade_resolves_and_returns_data()
    {
        // Bind a mocked Weather into the container so the facade uses it.
        $this->app->instance(\RakibDevs\Weather\Weather::class, $this->fakeWeather([$this->jsonResponse('current.json')]));

        $res = \RakibDevs\Weather\Facades\Weather::getCurrentByCity('dhaka');

        $this->assertSame(date('m/d/Y h:i A', 1609459200), $res->dt);
    }
}
