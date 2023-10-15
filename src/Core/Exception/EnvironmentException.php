<?php

namespace Schneidermanuel\Dynalinker\Core\Exception;

class EnvironmentException extends \Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}