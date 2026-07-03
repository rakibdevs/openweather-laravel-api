<?php

namespace RakibDevs\Weather;

use ArrayAccess;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JsonSerializable;

/**
 * A thin, opt-in fluent wrapper around an OpenWeatherMap response.
 *
 * It fully preserves backward compatibility: property access is proxied to the
 * underlying decoded object, so `$res->main->temp` keeps working exactly as before.
 * On top of that it adds dot-notation access, array/JSON conversion, collections
 * and a handful of convenience accessors for the common current-weather shape.
 *
 * @package openweather-laravel-api
 */
class WeatherResponse implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    /**
     * The original decoded response (stdClass, or an array for list endpoints like geocoding).
     *
     * @var mixed
     */
    protected $data;

    /**
     * Cached array representation.
     *
     * @var array|null
     */
    protected $array;

    /**
     * @param mixed $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Return the original, untouched decoded response (stdClass|array).
     *
     * @return mixed
     */
    public function raw()
    {
        return $this->data;
    }

    /**
     * Get a value using "dot" notation, e.g. get('main.temp') or get('weather.0.description').
     *
     * @param string|int|null $key
     * @param mixed           $default
     * @return mixed
     */
    public function get($key = null, $default = null)
    {
        if ($key === null) {
            return $this->toArray();
        }

        return Arr::get($this->toArray(), $key, $default);
    }

    /**
     * Determine whether the given "dot" notation key exists.
     *
     * @param string|int $key
     * @return bool
     */
    public function has($key): bool
    {
        return Arr::has($this->toArray(), $key);
    }

    /**
     * Wrap the response (or a sub-key of it) in a Laravel Collection.
     *
     * @param string|int|null $key
     * @return \Illuminate\Support\Collection
     */
    public function collect($key = null): Collection
    {
        $value = $key === null ? $this->toArray() : $this->get($key, []);

        return new Collection(is_array($value) ? $value : [$value]);
    }

    /**
     * Convenience accessors for the common current-weather response shape.
     * Each safely returns null when the field is not present on the response.
     */
    public function temperature()
    {
        return $this->get('main.temp');
    }

    public function feelsLike()
    {
        return $this->get('main.feels_like');
    }

    public function humidity()
    {
        return $this->get('main.humidity');
    }

    public function pressure()
    {
        return $this->get('main.pressure');
    }

    public function windSpeed()
    {
        return $this->get('wind.speed');
    }

    public function condition()
    {
        return $this->get('weather.0.main');
    }

    public function description()
    {
        return $this->get('weather.0.description');
    }

    public function icon()
    {
        return $this->get('weather.0.icon');
    }

    /**
     * Build the OpenWeatherMap icon URL, e.g. https://openweathermap.org/img/wn/04d@2x.png.
     *
     * @param string $suffix
     * @return string|null
     */
    public function iconUrl(string $suffix = '@2x')
    {
        $icon = $this->icon();

        return $icon ? "https://openweathermap.org/img/wn/{$icon}{$suffix}.png" : null;
    }

    public function city()
    {
        return $this->get('name');
    }

    public function country()
    {
        return $this->get('sys.country');
    }

    public function coordinates()
    {
        return $this->get('coord');
    }

    /**
     * Proxy property reads to the underlying object so existing code
     * such as `$res->main->temp` continues to work unchanged.
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (is_object($this->data)) {
            return $this->data->{$name} ?? null;
        }

        return is_array($this->data) ? ($this->data[$name] ?? null) : null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if (is_object($this->data)) {
            return isset($this->data->{$name});
        }

        return is_array($this->data) && isset($this->data[$name]);
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return Arr::has($this->toArray(), $offset);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $array = $this->toArray();
        if ($offset === null) {
            $array[] = $value;
        } else {
            $array[$offset] = $value;
        }
        $this->array = $array;
    }

    /**
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $array = $this->toArray();
        unset($array[$offset]);
        $this->array = $array;
    }

    /**
     * Convert the response to a plain (recursively arrayed) array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if ($this->array === null) {
            $this->array = json_decode(json_encode($this->data), true) ?: [];
        }

        return $this->array;
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Preserve the original JSON structure when re-encoding.
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}
