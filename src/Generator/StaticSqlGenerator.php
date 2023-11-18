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
        $this->db = DatabaseConnect::Get();

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
        if (count($this->filter) > 0) {
            $query = $query . " WHERE ";
            $filterQuery = array();
            foreach ($this->filter as $key => $filter) {
                if (is_string($filter)) {
                    $filterQuery[] = $key . " = '" . $this->db->Escape($filter) . "'";
                } else {
                    $filterQuery[] = $key . " = " . $this->db->Escape($filter);
                }
            }
            $query = $query . join(" AND ", $filterQuery);
        }
        $query = $query . ";";
        return $query;
    }

    public function GenerateInsertSqlQuery($tableName, $columns, $entity)
    {
        $query = "INSERT INTO " . $tableName . "(";
        $valuesToInsert = array();
        foreach ($columns as $property => $column) {
            if (isset($entity->{$property})) {
                $valuesToInsert[$column] = $entity->{$property};
            }
        }
        $query = $query . join(", ", array_keys($valuesToInsert));
        $query = $query . ") VALUES (";
        $escapedValues = array();
        foreach ($valuesToInsert as $value) {
            if (is_string($value)) {
                $escapedValues[] = "'" . $this->db->Escape($value) . "'";
            } else {
                $escapedValues[] = $this->db->Escape($value);
            }
        }
        $query = $query . join(", ", $escapedValues) . ");";
        return $query;

    }

    public function GenerateUpdateSqlQuery($tableName, array $mapping, $entity, $idProperty)
    {
        $query = "UPDATE " . $tableName . " SET ";
        $updateStatements = array();
        foreach ($mapping as $prop => $column) {
            if ($prop == $idProperty) {
                continue;
            }
            $propValue = $entity->{$prop};
            if (is_string($prop) && isset($propValue)) {
                $updateStatements[] = $column . " = '" . $propValue . "'";
            } else if (isset($propValue)) {
                $updateStatements[] = $column . " = " . $propValue;
            } else {
                $updateStatements[] = $column . " = null";
            }
        }
        $query = $query . join(", ", $updateStatements) . "WHERE $idProperty = " . $entity->{$idProperty};
        return $query;
    }

    public function GenerateDeleteStatement($tableName, $idProperty, $id)
    {
        $query = "DELETE FROM " . $tableName . " WHERE " . $idProperty . " = " . $id;
        return $query;
    }
}