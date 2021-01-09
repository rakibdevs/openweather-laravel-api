<?php
namespace RakibDevs\Weather\Src\Exceptions;

use Exception;

class WeatherException extends \Exception
{
	private $e;

    public function __construct($e){
        $this->e = $e;
    }

    public function render()
    {
        return $this->e->getResponse() == null?'Nothing found':$this->e->getResponse()->getBody(true);
    } 
    
}