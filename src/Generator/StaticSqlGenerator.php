<?php

namespace Schneidermanuel\Dynalinker\Generator;

class StaticSqlGenerator
{

    private $resultSet;
    private $tableName;
    private $filter;
    private $mysqli;

    public function __construct()
    {
        $this->resultSet = array();
        $this->filter = array();
        $this->mysqli = new \mysqli();
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
                $filterQuery[] = $key . " = " . mysqli_real_escape_string($this->mysqli, $filter);
            }
            $query = $query . join(" AND ", $filterQuery);
        }
        $query = $query . ";";
        return $query;
    }
}