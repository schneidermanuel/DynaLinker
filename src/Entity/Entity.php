<?php

namespace Schneidermanuel\Dynalinker\Entity;

#[\Attribute]
class Entity
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}