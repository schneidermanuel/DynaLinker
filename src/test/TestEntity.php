<?php

namespace Schneidermanuel\Dynalinker\test;

use Schneidermanuel\Dynalinker\Entity\Entity;
use Schneidermanuel\Dynalinker\Entity\Persist;

#[Entity("test")]
class TestEntity
{
    #[Persist("column")]
    public $Var;
}