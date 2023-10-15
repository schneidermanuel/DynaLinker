<?php

namespace Schneidermanuel\Dynalinker\Core;

use Dotenv\Dotenv;
use Schneidermanuel\Dynalinker\Core\Exception\EnvironmentException;
use Schneidermanuel\Dynalinker\Entity\EntityLoader;

class Dynalinker
{
    const MAX_PARENT_DIRECTORIES = 8;
    private $dotenv;
    private $entityLoader;

    private function __construct()
    {
        $this->InstantiateDotEnv();
        $this->entityLoader = new EntityLoader();
    }

    private static $dynalinker;

    public static function Get()
    {
        if (!isset(Dynalinker::$dynalinker)) {
            Dynalinker::$dynalinker = new Dynalinker();
        }
        return Dynalinker::$dynalinker;
    }
    private function InstantiateDotEnv()
    {
        $dir = "./";
        for ($i = 0; $i < self::MAX_PARENT_DIRECTORIES; $i++) {
            if (file_exists($dir . ".env")) {
                $this->dotenv = Dotenv::createImmutable($dir);
                $this->dotenv->load();
            }
            $dir = "../" . $dir;
        }
        throw new EnvironmentException("No .env file found. Loaded " . self::MAX_PARENT_DIRECTORIES . "directories from vendor");
    }
}