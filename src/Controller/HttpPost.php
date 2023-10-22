<?php

namespace Schneidermanuel\Dynalinker\Controller;

#[\Attribute]
class HttpPost
{
    public $path;

    public function __construct($path)
    {
        $this->path = $path;
    }
}