<?php
require '../vendor/autoload.php';

use Schneidermanuel\Dynalinker\Core\Dynalinker;
use Schneidermanuel\Dynalinker\Entity\Attribute\Entity;
use Schneidermanuel\Dynalinker\Entity\Attribute\Persist;
use Schneidermanuel\Dynalinker\Entity\Attribute\PrimaryKey;
use Schneidermanuel\Dynalinker\Entity\EntityStore;

#[Entity("user")]
class TestEntity
{
    #[Persist("userId")]
    #[PrimaryKey]
    public $testProperty;

    #[Persist("userName")]
    public $name;
    #[Persist("pwHash")]
    public $hash;
}

$dynalinker = Dynalinker::Get();
$store = $dynalinker->CreateStore(TestEntity::class);
$entity = $store->LoadById("1");
var_dump($entity);