PHP Database ORM
======
[![Latest Version](https://img.shields.io/github/tag/martenweijer/php-database-orm.svg?style=flat-square)](https://github.com/martenweijer/php-database-orm/tags)

A database ORM build in php.

## Requirements
The latest version of this package supports the following versions of PHP:
* PHP 8.1

## Usage
```php
require_once __DIR__ . '/vendor/autoload.php';

$context = new DatabaseContext(
    $conn = new MysqlConnection('host', 'dbname', 'user', 'pass')
);
$em = new SimpleEntityManager($context);
```

## Create an entity
```php
#[Entity('users')]
class User
{
    #[Id]
    public int $id;

    #[Column]
    public string $username;

    #[Column]
    public string $password;

    #[Column]
    public string $email;
}

$users = $em->load(User::class)->findAll();
```

## Save an entity
```php
$user = new User();
$user->username = 'test';
$user->password = 'test';
$user->email = 'test@test.com';

$users = $em->save($user);
```
