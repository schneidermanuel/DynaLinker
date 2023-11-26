<?php

namespace Schneidermanuel\Dynalinker\Core;

use function Schneidermanuel\Dynalinker\stringContains;

class DatabaseConnect
{
    private $mysql = "";
    private $text = "";

    public function Escape(string $string)
    {
        return $this->mysql->real_escape_string($string);
    }

    private static $instance;

    public static function Get()
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }
        self::$instance = new DatabaseConnect();
        return self::$instance;
    }

    private function __construct()
    {
        global $config;
        $mysql_auth = array();
        $this->mysql = mysqli_init();

        $this->mysql->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
        $this->mysql->options(MYSQLI_OPT_READ_TIMEOUT, 30);

        $mysql_auth['ip'] = $_ENV["DATABASE_IP"];
        $mysql_auth['username'] = $_ENV["DATABASE_USER"];
        $mysql_auth['password'] = $_ENV["DATABASE_PASSWORD"];
        $mysql_auth['db'] = $_ENV["DATABASE_DB"];

        try {
            $this->mysql->real_connect($mysql_auth['ip'], $mysql_auth['username'], $mysql_auth['password'], $mysql_auth['db']);
        } catch (Exception $e) {
            if (stringContains($e->getMessage(), "Unknown database")) {
                throw new SetupException("Given Database " . $config['mysql']['database'] . " doesn't exist");
            } else {
                throw new MySQLException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }
        }

        $this->mysql->set_charset("utf8");
    }

    public function query($sql): array
    {
        return $this->mysql->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function execute(string $sql)
    {
        $this->mysql->query($sql);
    }
}