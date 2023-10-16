<?php

namespace Schneidermanuel\Dynalinker\Entity;

use Couchbase\Scope;
use Schneidermanuel\Dynalinker\Core\Exception\MappingException;
use Schneidermanuel\Dynalinker\Entity\Attribute\Entity;
use Schneidermanuel\Dynalinker\Entity\Attribute\Persist;
use Schneidermanuel\Dynalinker\Entity\Attribute\PrimaryKey;

class EntityStore
{
    private \ReflectionClass $reflection;
    private string $className;
    private ScopeInvoker $scope;
    private array $mapping;
    private string $idProperty;

    public function __construct($className)
    {
        $this->className = $className;
        $this->reflection = new \ReflectionClass($className);
        $this->scope = new ScopeInvoker();
        $this->BuildMapping();
    }

    private function BuildMapping()
    {
        $this->mapping = array();
        $properties = $this->reflection->getProperties();
        foreach ($properties as $property) {
           $persistAttribute = $property->getAttributes(Persist::class);
           if (count($persistAttribute) > 0)
           {
               $columnName = $persistAttribute[0]->newInstance()->columnName;
               $this->mapping[$property->name] = $columnName;
               $primaryKeyAttribute = $property->getAttributes(PrimaryKey::class);
               if (count($primaryKeyAttribute) > 0)
               {
                   $this->idProperty = $property->name;
               }
           }
        }

        if (!isset($this->idProperty))
        {
            throw new MappingException("No Id Property found on type", null, $this->reflection->name);
        }

    }

    public function LoadById($id)
    {
        $tableName = $this->GetTableName();
        $pkColumnName = $this->mapping[$this->idProperty];
        $filter = array($pkColumnName => $id);
        $resultSet = $this->scope->InvokeWithFilter($tableName, $this->mapping, $filter);
        $result = $resultSet[0];
        return $this->CreateEntity($result);
    }

    public function LoadWithFilter($filterEntity)
    {
        $tableName = $this->GetTableName();
        $filter = $this->BuildFilterFromEntity($filterEntity);
        $resultSet = $this->scope->InvokeWithFilter($tableName, $this->mapping, $filter);
        $entities = array();
        foreach ($resultSet as $set) {
            $entities[] = $this->CreateEntity($set);
        }
        return $entities;
    }

    private function GetTableName()
    {
        $attributes = $this->reflection->getAttributes(Entity::class);
        if (count($attributes) != 1) {
            throw new MappingException("The class has an invalid entity signature", null, $this->className);
        }
        return $attributes[0]->newInstance()->name;
    }

    private function CreateEntity($resultSet)
    {
        $entityInstance = new $this->className();
        foreach ($this->mapping as $property => $column) {
            if (isset($resultSet[$column])) {
                $entityInstance->$property = $resultSet[$column];
            }
        }
        return $entityInstance;
    }

    private function BuildFilterFromEntity($filterEntity)
    {
        $filter = array();
        $properties = $this->reflection->getProperties();
        foreach ($properties as $property) {
            $propertyValue = $filterEntity->{$property->name};
            if (isset($propertyValue)) {
                $columnName = $property->getAttributes(Persist::class)[0]->newInstance()->columnName;
                $filter[$columnName] = $propertyValue;
            }
        }
        return $filter;
    }

}