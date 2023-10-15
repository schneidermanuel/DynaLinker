<?php

namespace Schneidermanuel\Dynalinker\Entity;

use Schneidermanuel\Dynalinker\Core\Exception\MappingException;
use Schneidermanuel\Dynalinker\Entity\Attribute\Entity;
use Schneidermanuel\Dynalinker\Entity\Attribute\Persist;
use Schneidermanuel\Dynalinker\Entity\Attribute\PrimaryKey;

class EntityStore
{
    private $reflection;
    private $className;
    private $scope;

    public function __construct($className)
    {
        $this->className = $className;
        $this->reflection = new \ReflectionClass($className);
        $this->scope = new ScopeInvoker();
    }

    public function LoadById($id)
    {
        $tableName = $this->GetTableName();
        $pkColumnName = $this->GetPrimaryKeyColumn();
        $allColumns = $this->GetAttributes();
        $filter = array($pkColumnName => $id);
        $resultSet = $this->scope->InvokeWithFilter($tableName, $allColumns, $filter);
        $result = $resultSet[0];
        return $this->CreateEntity($result);
    }

    public function LoadWithFilter($filterEntity)
    {
        $tableName = $this->GetTableName();
        $allColumns = $this->GetAttributes();
        $filter = $this->BuildFilterFromEntity($filterEntity);
        $resultSet = $this->scope->InvokeWithFilter($tableName, $allColumns, $filter);
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

    private function GetAttributes()
    {
        $results = array();
        $properties = $this->reflection->getProperties();
        foreach ($properties as $prop) {
            $columnName = $this->GetColumnName($prop);
            if (isset($columnName)) {
                $results[$prop->name] = $columnName;
            }
        }
        return $results;
    }

    private function GetPrimaryKeyColumn()
    {
        $properties = $this->reflection->getProperties();
        foreach ($properties as $prop) {
            $attributes = $prop->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getName() == PrimaryKey::class) {
                    $columnName = $this->GetColumnName($prop);
                    if (!isset($columnName)) {
                        throw new MappingException("The primary Key has no column defined", "id", get_called_class());
                    }
                    return $columnName;
                }
            }
        }
        throw new MappingException("The class '" . get_called_class() . "' has no id attribute", "id", get_called_class());
    }

    private function GetColumnName(\ReflectionProperty $property)
    {
        $attributes = $property->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getName() == Persist::class) {
                return $attribute->newInstance()->columnName;
            }
        }
        return null;
    }

    private function CreateEntity($resultSet)
    {
        $attributes = $this->GetAttributes();
        $entityInstance = new $this->className();
        foreach ($attributes as $property => $column) {
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