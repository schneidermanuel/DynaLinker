<?php

namespace Schneidermanuel\Dynalinker\Entity;

#[\Attribute]
class Persist
{
    public $columnName;

    public function __construct($columnName)
    {
        $this->columnName = $columnName;
    }
}