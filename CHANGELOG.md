# Changelog

All notable changes to `rakibdevs/openweather-laravel-api` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - Unreleased

Non-breaking modernization: the entire public API (`new Weather()`, every `get*` method,
their signatures, and the `stdClass` response objects) is unchanged. Existing apps continue
to work without code changes.

### Added
- Optional **fluent response** via `->fluent()`, returning a `WeatherResponse` wrapper with
  dot-notation `get()`/`has()`, `toArray()`/`toJson()`/`collect()`/`raw()`, ArrayAccess and
  convenience getters (`temperature()`, `description()`, `iconUrl()`, `city()`, …). Property
  access such as `$res->main->temp` is preserved, and responses stay `stdClass` by default.
- Optional `Weather` **facade** (`RakibDevs\Weather\Facades\Weather`) and a `weather` container
  binding, auto-registered via Laravel package discovery.
- Fluent, **per-call overrides** `->units(string $unit)` and `->lang(string $lang)` that apply to
  a single request and then reset.
- Optional **response caching** driven by config keys `cache_enabled` (default `false`) and
  `cache_ttl` (default `600` seconds). Disabled by default, so behavior is unchanged.
- Correctly spelled config key `pollution_api_version` (the misspelled `polution_api_version`
  is still honored as a fallback).
- Env-var fallbacks: `OPENWEATHER_API_KEY` / `OPENWEATHER_API_LANG` are preferred but fall back
  to the legacy misspelled `OPENWAETHER_API_KEY` / `OPENWAETHER_API_LANG` so existing `.env`
  files keep working.
- Injectable Guzzle client on `Weather` / `WeatherClient` constructors for testing and DI.
- Real, mocked test suite (Orchestra Testbench + Guzzle `MockHandler`) covering every endpoint,
  plus PHPStan static analysis and a GitHub Actions matrix for PHP 8.0–8.4.

### Changed
- **Requires PHP `^8.0`** (8.0–8.4) and **Guzzle `^7.5`**; dropped end-of-life PHP 7.x and Guzzle 6.
- `WeatherServiceProvider` now `mergeConfigFrom(...)` so the package works before `vendor:publish`.
- `WeatherClient::fetch()` now throws `WeatherException` on non-200 responses or undecodable
  bodies instead of returning `null` (prevents downstream "read property on null" errors).

### Fixed
- `getAirPollutionByCord()` with a start/end range now queries the historical
  `/air_pollution/history` endpoint instead of the current-conditions endpoint, which
  ignored the range and returned only current data.
- PHP 8.4 implicit-nullable deprecations: `getAirPollutionByCord()`, `getGeoByCity()` and
  `getGeoByCord()` now declare explicit `?string` parameters.
- `WeatherFormat` no longer emits warnings when optional One Call blocks (`minutely`/`hourly`/
  `daily`) are absent, and `dt()` safely coerces string timestamps.

## [1.x]
- Legacy releases supporting PHP 7.2–8.0 and Guzzle 6/7.
