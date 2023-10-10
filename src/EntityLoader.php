<?php

namespace Schneidermanuel\Dynalinker;

class EntityLoader
{
    private $databaseConnect;
    public function __construct()
    {
        $this->databaseConnect = new DatabaseConnect();
    }
}