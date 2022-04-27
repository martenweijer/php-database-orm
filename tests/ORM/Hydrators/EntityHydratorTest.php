<?php

namespace Electronics\Database\ORM\Hydrators;

use Electronics\Database\Connections\Connection;
use Electronics\Database\DBAL\Builder;
use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\DBAL\Mysql\MysqlBuilderFactory;
use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Annotations\OneToMany;
use Electronics\Database\ORM\Annotations\OneToOne;
use Electronics\Database\ORM\Collections\EntityCollection;
use Electronics\Database\ORM\Collections\ProxyCollection;
use Electronics\Database\ORM\Configurations\AnnotationConfiguration;
use Electronics\Database\ORM\DatabaseContext;
use Electronics\Database\ORM\EntityManager;
use Electronics\Database\ORM\Proxy\DummyProxyFactory;
use Electronics\Database\ORM\Proxy\EntityProxyFactory;
use Electronics\Database\ORM\Repositories\Repository;
use Electronics\Database\ORM\SimpleEntityManager;
use Electronics\Database\ORM\Typings\Fetch;
use Electronics\Database\ORM\Typings\SimpleValueConverter;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;
use PHPUnit\Framework\TestCase;

class EntityHydratorTest extends TestCase
{
    function testHydrate(): void
    {
        $conf = new AnnotationConfiguration();
        $hydrator = new EntityHydrator($conf, new SimpleValueConverter(), new UnitOfWork(), new EntityProxyFactory());
        $entityMap = $conf->retrieveEntityMap(EntityHydratorTestEntity::class);

        $row = [
            'id' => 1,
            'username' => 'foo',
            'password' => 'bla',
            'email' => null,
            'rank' => null,
            'isActive' => null,
            'created_at' => null,
            'updated_at' => '2021-01-01',
        ];

        $entity = $hydrator->hydrate($row, $entityMap, $this->createMock(EntityManager::class));

        $this->assertEquals(1, $entity->id);
        $this->assertEquals('foo', $entity->username);
        $this->assertEquals('bla', $entity->password);
        $this->assertEquals(null, $entity->email);
        $this->assertEquals(0, $entity->rank);
        $this->assertNotNull($entity->rank);
        $this->assertEquals(false, $entity->isActive);
        $this->assertEquals(null, $entity->createdAt);
        $this->assertEquals(new \DateTime('2021-01-01'), $entity->updatedAt);
    }

    function testHydrateOneToOne(): void
    {
        $conf = new AnnotationConfiguration();
        $hydrator = new EntityHydrator($conf, new SimpleValueConverter(), new UnitOfWork(), new DummyProxyFactory());
        $entityMap = $conf->retrieveEntityMap(EntityHydratorTestToOneEntity::class);

        $row = [
            'id' => 1,
            'username' => 'foo',
            'user_id' => 5,
        ];

        $toOneEntity = new EntityHydratorTestEntity();
        $toOneEntity->id = 5;
        $toOneEntity->username = 'bar';
        $em = $this->createMock(EntityManager::class);
        $repository = $this->createMock(Repository::class);
        $em->method('load')->willReturn($repository);
        $repository->method('findBy')->willReturn([$toOneEntity]);
        $em->method('find')->willReturn($toOneEntity);

        $entity = $hydrator->hydrate($row, $entityMap, $em);

        $this->assertEquals(1, $entity->id);
        $this->assertEquals(5, $entity->user->id);
        $this->assertEquals('bar', $entity->user->username);
    }

    function testLazyLoading(): void
    {
        $uow = new UnitOfWork();
        $conf = new AnnotationConfiguration();
        $hydrator = new EntityHydrator($conf, new SimpleValueConverter(), $uow, new EntityProxyFactory());
        $entityMap = $conf->retrieveEntityMap(EntityHydratorTestToOneEntity::class);

        $row = [
            'id' => 1,
            'username' => 'foo',
            'user_id' => 5,
        ];

        $em = new SimpleEntityManager(new DatabaseContext(
            new EntityHydratorTestConnection(), null, $uow
        ));

        $entity = $hydrator->hydrate($row, $entityMap, $em);

        $this->assertEquals(1, $entity->id);
        $this->assertEquals(5, $entity->user->id);
        $this->assertEquals('foo', $entity->user->getUsername());
    }

    function testHydrateOneToMany(): void
    {
        $uow = new UnitOfWork();
        $conf = new AnnotationConfiguration();
        $hydrator = new EntityHydrator($conf, new SimpleValueConverter(), $uow, new EntityProxyFactory());
        $entityMap = $conf->retrieveEntityMap(EntityHydratorTestOne::class);

        $row = [
            'id' => 1
        ];

        $em = new SimpleEntityManager(new DatabaseContext(
            new EntityHydratorTestConnection(), null, $uow
        ));

        $entity = $hydrator->hydrate($row, $entityMap, $em);

        $this->assertEquals(1, $entity->id);
        $this->assertInstanceOf(ProxyCollection::class, $entity->many);
        $this->assertEquals(1, $entity->many->count());

        $this->assertEquals(5, $entity->many[0]->id);
        $this->assertEquals('foo', $entity->many[0]->username);
    }
}

#[Entity('users')]
class EntityHydratorTestEntity
{
    #[Id]
    public ?int $id;

    #[Column]
    public string $username;

    #[Column]
    public string $password;

    #[Column]
    public ?string $email;

    #[Column]
    public int $rank;

    #[Column]
    public bool $isActive;

    #[Column('created_at')]
    public ?\DateTime $createdAt;

    #[Column('updated_at')]
    public \DateTime $updatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}

#[Entity('user_details')]
class EntityHydratorTestToOneEntity
{
    #[Id]
    public ?int $id;

    #[Column]
    public string $username;

    #[OneToOne(fetchType: Fetch::LAZY)]
    public EntityHydratorTestEntity $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getUser(): EntityHydratorTestEntity
    {
        return $this->user;
    }
}

class EntityHydratorTestConnection implements Connection
{
    public Builder $builder;

    public function execute(Builder $builder): \PDOStatement
    {
        $this->builder = $builder;
        return new EntityHydratorTestStatement();
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

class EntityHydratorTestStatement extends \PDOStatement
{
    public function fetchAll($mode = \PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        return [
            [
                'id' => 5,
                'username' => 'foo',
                'password' => 'bla',
                'email' => null,
                'rank' => null,
                'isActive' => null,
                'created_at' => null,
                'updated_at' => '2021-01-01',
            ]
        ];
    }
}

#[Entity('EntityHydratorTestOne')]
class EntityHydratorTestOne
{
    #[Id]
    public ?int $id;

    #[OneToMany(EntityHydratorTestMany::class, column: 'one_id')]
    public EntityCollection $many;
}

#[Entity('EntityHydratorTestMany')]
class EntityHydratorTestMany
{
    #[Id]
    public ?int $id;

    #[Column]
    public string $username;

    public EntityHydratorTestOne $one;
}
