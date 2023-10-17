<?php
require '../vendor/autoload.php';

use Schneidermanuel\Dynalinker\Core\Dynalinker;
use Schneidermanuel\Dynalinker\Entity\Attribute\Entity;
use Schneidermanuel\Dynalinker\Entity\Attribute\Persist;
use Schneidermanuel\Dynalinker\Entity\Attribute\PrimaryKey;

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
$entity = new TestEntity();
$entity->name = "Test";
$entity->hash = "FJAF";
$id = $store->SaveOrUpdate($entity);
var_dump($store->LoadById($id));