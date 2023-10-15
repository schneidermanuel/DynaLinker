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
        $result = $this->scope->InvokeWithFilter($tableName, $allColumns, $filter);
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
        $result = array();
        $properties = $this->reflection->getProperties();
        foreach ($properties as $prop) {
            $columnName = $this->GetColumnName($prop);
            if (isset($columnName)) {
                $result[$prop->name] = $columnName;
            }
        }
        return $result;
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
}