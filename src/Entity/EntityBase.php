<?php

namespace Schneidermanuel\Dynalinker\Entity;

class EntityBase
{
    private $reflection;
    public function __construct()
    {
        $this->reflection = new \ReflectionClass($this);
    }

    public function LoadById($id)
    {
        $columnName = $this->GetPrimaryKeyColumn();
        echo $columnName;
    }

    private function GetPrimaryKeyColumn()
    {
        $properties = $this->reflection->getProperties();
        foreach ($properties as $prop) {
            $attributes = $prop->getAttributes();
            foreach ($attributes as $attribute) {
                if ($attribute->getName() == "PrimaryKey") {
                    return $this->GetColumnName($prop);
                }
            }
        }
        throw new MappingException("The class '". get_called_class() . "' has no id attribute", "id", get_called_class());
    }
    private function GetColumnName(\ReflectionProperty $property){
        $attributes = $property->getAttributes();
        foreach ($attributes as $attribute)
        {
            if ($attribute->getName() == "Persist")
            {
                return $attribute->getArguments()["columnName"];
            }
        }
        throw new MappingException("The property with the name '" . $property->getName() . "' has no persist attribute defined", $property->getName());
    }
}