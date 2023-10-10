<?php

namespace Schneidermanuel\Dynalinker;

use Dotenv\Dotenv;

class Dynalinker
{
    private $dotenv;
    private $entityLoader;
    private function __construct()
    {
        $this->dotenv = Dotenv::createImmutable(getcwd());
        $this->entityLoader = new EntityLoader();
    }

    private static $dynalinker;
    public static function Get()
    {
        if (!isset(Dynalinker::$dynalinker))
        {
            Dynalinker::$dynalinker = new Dynalinker();
        }
        return Dynalinker::$dynalinker;
    }
}