<?php

namespace Schneidermanuel\Dynalinker\Core\Exception;

class MappingException extends \Exception
{
    public $propertyName;
    public $className;
    public function __construct(string $message, $propertyName, $className)
    {
        $this->propertyName = $propertyName;
        $this->className = $className;
        parent::__construct($message);
    }
}