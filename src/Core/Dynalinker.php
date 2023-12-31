<?php

namespace Schneidermanuel\Dynalinker\Core;

use Dotenv\Dotenv;
use Schneidermanuel\Dynalinker\Controller\CallMapper;
use Schneidermanuel\Dynalinker\Core\Exception\EnvironmentException;
use Schneidermanuel\Dynalinker\Entity\EntityStore;

class Dynalinker
{
    const MAX_PARENT_DIRECTORIES = 8;
    private $dotenv;
    private array $stores;
    private $callMapper;
    private $db;

    private function __construct()
    {
        $this->stores = array();
        $this->InstantiateDotEnv();
        if ($_ENV["RUN_TYPE"] == "DEBUG") {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
        $this->callMapper = new CallMapper();
        $this->db = DatabaseConnect::Get();
    }

    private static $dynalinker;

    public function CreateStore($entityName)
    {
        if (array_key_exists($entityName, $this->stores)) {
            return $this->stores[$entityName];
        }
        $newStore = new EntityStore($entityName);
        $this->stores[$entityName] = $newStore;
        return $newStore;
    }

    public static function Get(): Dynalinker
    {
        if (!isset(Dynalinker::$dynalinker)) {
            Dynalinker::$dynalinker = new Dynalinker();
        }
        return Dynalinker::$dynalinker;
    }

    private function InstantiateDotEnv()
    {
        $dir = dirname(__FILE__);
        for ($i = 0; $i < self::MAX_PARENT_DIRECTORIES; $i++) {
            if (file_exists($dir . DIRECTORY_SEPARATOR . ".env")) {
                $this->dotenv = Dotenv::createImmutable($dir);
                $this->dotenv->load();
                return;
            }
            $dir = dirname($dir);
        }
        throw new EnvironmentException("No .env file found. Loaded " . self::MAX_PARENT_DIRECTORIES . "directories from vendor");
    }

    public function AddController($path, $class)
    {
        $this->callMapper->RegisterController($path, $class);
    }

    public function Run()
    {
        $this->callMapper->MapCall(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
    }

    public function Query($sql): array
    {
        return $this->db->query($sql);
    }
}
