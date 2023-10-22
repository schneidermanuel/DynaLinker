<?php

namespace Schneidermanuel\Dynalinker\Controller;

#[\Attribute]
class HttpGet
{
    public $path;

    public function __construct($path)
    {
        $this->path = $path;
    }
}