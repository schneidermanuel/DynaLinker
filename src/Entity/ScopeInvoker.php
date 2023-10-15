<?php

namespace Schneidermanuel\Dynalinker\Entity;

use Schneidermanuel\Dynalinker\Core\DatabaseConnect;
use Schneidermanuel\Dynalinker\Generator\StaticSqlGenerator;

class ScopeInvoker
{
    private $generator;
    private $db;

    public function __construct()
    {
        $this->generator = new StaticSqlGenerator();
        $this->db = new DatabaseConnect();
    }

    public function InvokeWithFilter($tableName, $results, $filter)
    {
        $sql = $this->generator->GenerateSingleTableSqlQuery($tableName, $results, $filter);
        $result = $this->db->query($sql);
        return $result;
    }
}