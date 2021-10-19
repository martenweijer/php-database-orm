<?php

namespace ORM\Repositories;

use Electronics\Database\Connections\Connection;
use Electronics\Database\DBAL\Builder;
use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\DBAL\Mysql\MysqlBuilderFactory;
use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\DatabaseContext;
use Electronics\Database\ORM\Repositories\EntityRepository;
use PDO;
use PHPUnit\Framework\TestCase;

class EntityRepositoryTest extends TestCase
{
    function testFind(): void
    {
        $conn = new EntityRepositoryTestConnection();
        $dbContext = new DatabaseContext($conn);
        $repository = new EntityRepository(EntityRepositoryTestEntity::class, $dbContext);
        $entity = $repository->find(1);

        $entityCheck = new EntityRepositoryTestEntity();
        $entityCheck->id = 1;
        $entityCheck->username = 'foo';

        $this->assertEquals($entityCheck, $entity);
    }

    function testFindAll(): void
    {
        $conn = new EntityRepositoryTestConnection();
        $dbContext = new DatabaseContext($conn);
        $repository = new EntityRepository(EntityRepositoryTestEntity::class, $dbContext);
        $entities = $repository->findAll();

        $entityCheck = new EntityRepositoryTestEntity();
        $entityCheck->id = 1;
        $entityCheck->username = 'foo';

        $this->assertEquals([$entityCheck], $entities);
    }

    function testFindBy(): void
    {
        $conn = new EntityRepositoryTestConnection();
        $dbContext = new DatabaseContext($conn);
        $repository = new EntityRepository(EntityRepositoryTestEntity::class, $dbContext);
        $repository->findBy(['is_active' => false], 10, ['username' => 'desc']);

        $this->assertEquals('select * from `users` where `is_active` = :param_1 order by `username` desc limit :param_0', $conn->builder->generateSql());
        $this->assertEquals([':param_0' => 10, ':param_1' => 0], $conn->builder->getParameters());
    }
}

class EntityRepositoryTestConnection implements Connection
{
    public Builder $builder;

    public function execute(Builder $builder): \PDOStatement
    {
        $this->builder = $builder;
        return new EntityRepositoryTestStatement();
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

class EntityRepositoryTestStatement extends \PDOStatement
{
    public function fetchAll($mode = PDO::FETCH_BOTH, ...$args)
    {
        return [
            [
                'id' => '1',
                'user_name' => 'foo'
            ]
        ];
    }
}

#[Entity('users')]
class EntityRepositoryTestEntity
{
    #[Id]
    public $id;
    #[Column('user_name')]
    public $username;
}
