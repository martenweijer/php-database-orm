<?php

namespace ORM\Persisting;

use Electronics\Database\Connections\Connection;
use Electronics\Database\DBAL\Builder;
use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\DBAL\Mysql\MysqlBuilderFactory;
use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Configurations\AnnotationConfiguration;
use Electronics\Database\ORM\Persisting\EntityPersister;
use Electronics\Database\ORM\Typings\SimpleValueConverter;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;
use PHPUnit\Framework\TestCase;

class EntityPersisterTest extends TestCase
{
    function testInsert(): void
    {
        $conn = new EntityPersisterTestConnection();
        $conf = new AnnotationConfiguration();
        $valueConverter = new SimpleValueConverter();
        $uow = new UnitOfWork();
        $persister = new EntityPersister($conn, $conf, $valueConverter, $uow);

        $entity = new EntityPersisterTestEntity();
        $entity->username = 'test';
        $persister->insert($entity);

        $this->assertEquals(1, $entity->id);

        $this->assertEquals('insert into `users` (`user_name`) values (:param_0)', $conn->builder->generateSql());
        $this->assertEquals([':param_0' => 'test'], $conn->builder->getParameters());
    }

    function testUpdate(): void
    {
        $conn = new EntityPersisterTestConnection();
        $conf = new AnnotationConfiguration();
        $valueConverter = new SimpleValueConverter();
        $uow = new UnitOfWork();
        $persister = new EntityPersister($conn, $conf, $valueConverter, $uow);

        $entity = new EntityPersisterTestEntity();
        $entity->id = 5;
        $entity->username = 'test';
        $persister->update($entity);

        $this->assertEquals('update `users` set `user_name` = :param_1 where `id` = :param_0', $conn->builder->generateSql());
        $this->assertEquals([':param_0' => '5', ':param_1' => 'test'], $conn->builder->getParameters());
    }

    function testDelete(): void
    {
        $conn = new EntityPersisterTestConnection();
        $conf = new AnnotationConfiguration();
        $valueConverter = new SimpleValueConverter();
        $uow = new UnitOfWork();
        $persister = new EntityPersister($conn, $conf, $valueConverter, $uow);

        $entity = new EntityPersisterTestEntity();
        $entity->id = 5;
        $persister->delete($entity);

        $this->assertEquals('delete from `users` where `id` = :param_0', $conn->builder->generateSql());
        $this->assertEquals([':param_0' => '5'], $conn->builder->getParameters());
    }
}

class EntityPersisterTestConnection implements Connection
{
    public Builder $builder;

    public function execute(Builder $builder): \PDOStatement
    {
        $this->builder = $builder;
        return new \PDOStatement();
    }

    public function retrieveLastInsertId(): string
    {
        return '1';
    }

    public function getBuilderFactory(): BuilderFactory
    {
        return new MysqlBuilderFactory();
    }

}

#[Entity('users')]
class EntityPersisterTestEntity
{
    #[Id]
    public $id;
    #[Column('user_name')]
    public $username;
}
