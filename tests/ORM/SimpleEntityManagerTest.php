<?php

namespace ORM;

use Electronics\Database\Connections\Connection;
use Electronics\Database\DBAL\Builder;
use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\DBAL\Mysql\MysqlBuilderFactory;
use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\DatabaseContext;
use Electronics\Database\ORM\Repositories\EntityRepository;
use Electronics\Database\ORM\SimpleEntityManager;
use PHPUnit\Framework\TestCase;

class SimpleEntityManagerTest extends TestCase
{
    function testFind(): void
    {
        $conn = new SimpleEntityManagerTestConnection();
        $dbContext = new DatabaseContext($conn);
        $em = new SimpleEntityManager($dbContext);
        $entity = $em->find(SimpleEntityManagerTestEntity::class, 1);

        $entityCheck = new SimpleEntityManagerTestEntity();
        $entityCheck->id = 1;
        $entityCheck->username = 'foo';

        $this->assertEquals($entityCheck, $entity);
    }

    function testLoad(): void
    {
        $conn = new SimpleEntityManagerTestConnection();
        $dbContext = new DatabaseContext($conn);
        $em = new SimpleEntityManager($dbContext);
        $repository = $em->load(SimpleEntityManagerTestEntity::class);

        $repositoryCheck = new EntityRepository(SimpleEntityManagerTestEntity::class, $dbContext);
        $this->assertEquals($repositoryCheck, $repository);
    }

    function testSave(): void
    {
        $conn = new SimpleEntityManagerTestConnection();
        $dbContext = new DatabaseContext($conn);
        $em = new SimpleEntityManager($dbContext);

        $entity = new SimpleEntityManagerTestEntity();
        $entity->username = 'foo';

        $em->save($entity);

        $this->assertEquals(1, $entity->id);
        $this->assertTrue($dbContext->getUnitOfWork()->isEntityAddedToIdentityMap(SimpleEntityManagerTestEntity::class, 1));
    }
}

class SimpleEntityManagerTestConnection implements Connection
{
    public Builder $builder;

    public function execute(Builder $builder): \PDOStatement
    {
        $this->builder = $builder;
        return new SimpleEntityManagerTestStatement();
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

class SimpleEntityManagerTestStatement extends \PDOStatement
{
    public function fetchAll($mode = \PDO::FETCH_BOTH, ...$args)
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
class SimpleEntityManagerTestEntity
{
    #[Id]
    public $id;
    #[Column('user_name')]
    public $username;
}
