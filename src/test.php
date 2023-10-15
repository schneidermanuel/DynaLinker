<?php
require '../vendor/autoload.php';

use Schneidermanuel\Dynalinker\Entity\Attribute\Entity;
use Schneidermanuel\Dynalinker\Entity\Attribute\Persist;
use Schneidermanuel\Dynalinker\Entity\Attribute\PrimaryKey;
use Schneidermanuel\Dynalinker\Entity\EntityStore;

#[Entity("testTable")]
class TestEntity
{
    #[Persist("id")]
    #[PrimaryKey]
    private $testProperty;

    #[Persist("username")]
    private $name;
}

$store = new EntityStore(TestEntity::class);
$store->LoadById("1");