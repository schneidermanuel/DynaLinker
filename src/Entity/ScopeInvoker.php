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
        $this->db = DatabaseConnect::Get();
    }

    public function InvokeWithFilter($tableName, $results, $filter)
    {
        $sql = $this->generator->GenerateSingleTableSqlQuery($tableName, $results, $filter);
        $result = $this->db->query($sql);
        return $result;
    }

    public function SaveEntity($entity, $columns, $tableName)
    {
        $sql = $this->generator->GenerateInsertSqlQuery($tableName, $columns, $entity);
        $this->db->execute($sql);
        return $this->db->query("SELECT LAST_INSERT_ID() as id;")[0]["id"];
    }

    public function UpdateEntity($entity, array $mapping, $tableName, $idProperty)
    {
        $sql = $this->generator->GenerateUpdateSqlQuery($tableName, $mapping, $entity, $idProperty);
        $this->db->execute($sql);
        return $entity->{$idProperty};
    }

    public function DeleteById($id, $idProperty, $tableName)
    {
        $sql = $this->generator->GenerateDeleteStatement($tableName, $idProperty, $id);
        $this->db->execute($sql);
    }

    public function LoadWithQuery($sql)
    {
        $resultSet = $this->db->query($sql);
        return $resultSet;
    }
}