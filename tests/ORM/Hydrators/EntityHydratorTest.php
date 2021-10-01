<?php

namespace Electronics\Database\ORM\Hydrators;

use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Configurations\AnnotationConfiguration;
use Electronics\Database\ORM\Typings\ColumnType;
use Electronics\Database\ORM\Typings\SimpleValueConverter;
use Electronics\Database\ORM\UnitOfWork\EntityRegistry;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;
use PHPUnit\Framework\TestCase;

class EntityHydratorTest extends TestCase
{
    public function testHydrate(): void
    {
        $hydrator = new EntityHydrator(new SimpleValueConverter(), new UnitOfWork(), new EntityRegistry());
        $conf = new AnnotationConfiguration();
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

        $entity = $hydrator->hydrate($row, $entityMap);

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
}
