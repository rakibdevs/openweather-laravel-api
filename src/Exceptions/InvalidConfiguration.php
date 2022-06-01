<?php

namespace RakibDevs\Weather\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        //
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        return response([
            'message' => 'Invalid API Key. Get a free Open Weather Map API key : https://openweathermap.org/price.',
        ]);
    }
}
