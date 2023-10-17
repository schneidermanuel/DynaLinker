# DynaLinker

This is a minimalistic library for creating simple php applications.

### Features

- Minimalistic ORM
- Environment Variables
- Call mapping

### Basic usage:

Get the Singleton Dynalinker object and use its functions to generate all the objects.
Supports caching.

```php
$dynalinker = Dynalinker::Get();
```

#### Minimalistic ORM

> Only Supports MySQL and MariaDB

Create an Entity class an annotate it with the Entity attribute, providing the table name as parameter.
Annotate every property with persist and provide the column name as attribute. Properties without the persist attribute won't be stored on the database. 
Put the PrimaryKey Attribute on exactly one attribute to mark it as primary key. 

```php
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
```

Generate a store object for the entity. Each store object is only responsible for one entity. 
Basic CRUD-Actions are available on it. 

```php
$store = $dynalinker->CreateStore(TestEntity::class);
$entity = new TestEntity();
$entity->name = "Test";
$entity->hash = "FJAF";
$id = $store->SaveOrUpdate($entity);
var_dump($store->LoadById($id));
```

#### Environment Variables

Once, Dynalinker::Get(); has been called, all Variables from a .env file lying on the same directory as the composer folder will be available in the $_ENV superglobal. 


#### Call mapping

Will be done soon

### License

MIT License. 
