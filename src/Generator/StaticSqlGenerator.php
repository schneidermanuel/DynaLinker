<?php

namespace Schneidermanuel\Dynalinker\Generator;

use Schneidermanuel\Dynalinker\Core\DatabaseConnect;
use Schneidermanuel\Dynalinker\Entity\ScopeInvoker;

class StaticSqlGenerator
{

    private $resultSet;
    private $tableName;
    private $filter;
    private $db;
    public function __construct()
    {
        $this->resultSet = array();
        $this->filter = array();
        $this->db = new DatabaseConnect();

    }

    public function GenerateSingleTableSqlQuery($tableName, $results, $filter)
    {
        $this->tableName = $tableName;
        foreach ($results as $result) {
            $this->resultSet[] = $result;
        }
        $this->filter = $filter;
        return $this->Execute();
    }

    private function Execute()
    {
        $query = "SELECT ";
        $query = $query . join(", ", $this->resultSet);
        $query = $query . " FROM " . $this->tableName;
        if (count($this->filter) > 0)
        {
            $query = $query . " WHERE ";
            $filterQuery = array();
            foreach ($this->filter as $key => $filter) {
                if (is_string($filter))
                {
                    $filterQuery[] = $key . " = '" . $this->db->Escape($filter) . "'";
                }
                else
                {
                    $filterQuery[] = $key . " = " . $this->db->Escape($filter);
                }
            }
            $query = $query . join(" AND ", $filterQuery);
        }
        $query = $query . ";";
        return $query;
    }
}