<?php

namespace Electronics\Database\ORM\Configurations;

use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Annotations\OneToMany;
use Electronics\Database\ORM\Annotations\OneToOne;
use Electronics\Database\ORM\Collections\EntityCollection;
use Electronics\Database\ORM\Mappings\OneToManyMap;
use Electronics\Database\ORM\Mappings\OneToOneMap;
use Electronics\Database\ORM\Typings\ColumnType;
use Electronics\Database\ORM\Typings\Fetch;
use PHPUnit\Framework\TestCase;

class AnnotationConfigurationTest extends TestCase
{
    function testEntityMap(): void
    {
        $conf = new AnnotationConfiguration();
        $entityMap = $conf->retrieveEntityMap(AnnotationConfigurationTestEntity::class);

        $this->assertEquals(AnnotationConfigurationTestEntity::class, $entityMap->getClass());
        $this->assertEquals('users', $entityMap->getTable());

        $this->assertEquals('id', $entityMap->getIdentity()->getName());
        $this->assertEquals('id', $entityMap->getIdentity()->getColumn());

        $this->assertEquals(7, count($entityMap->getProperties()));

        $username = $entityMap->getProperty('username');
        $this->assertEquals('username', $username->getName());
        $this->assertEquals('user_name', $username->getColumn());
        $this->assertEquals(ColumnType::STRING, $username->getColumnType());

        $rank = $entityMap->getProperty('rank');
        $this->assertEquals('rank', $rank->getName());
        $this->assertEquals('rank', $rank->getColumn());
        $this->assertEquals(ColumnType::INT, $rank->getColumnType());

        $rank = $entityMap->getProperty('float');
        $this->assertEquals(ColumnType::FLOAT, $rank->getColumnType());

        $rank = $entityMap->getProperty('string');
        $this->assertEquals(ColumnType::STRING, $rank->getColumnType());

        $rank = $entityMap->getProperty('isActive');
        $this->assertEquals(ColumnType::BOOL, $rank->getColumnType());

        $rank = $entityMap->getProperty('rank2');
        $this->assertEquals(ColumnType::INT, $rank->getColumnType());

        $rank = $entityMap->getProperty('date');
        $this->assertEquals(ColumnType::DATETIME, $rank->getColumnType());
    }

    function testOneToOneMap(): void
    {
        $conf = new AnnotationConfiguration();
        $entityMap = $conf->retrieveEntityMap(AnnotationConfigurationTestToOneEntity::class);

        $this->assertEquals(1, count($entityMap->getOneToOneMappings()));

        $map = new OneToOneMap('user', AnnotationConfigurationTestEntity::class, 'user_id', Fetch::EAGER, new \ReflectionProperty(AnnotationConfigurationTestToOneEntity::class, 'user'));
        $this->assertEquals($map, $entityMap->getOneToOneMappings()[0]);
    }

    function testOneToManyMap(): void
    {
        $conf = new AnnotationConfiguration();
        $entityMap = $conf->retrieveEntityMap(AnnotationConfigurationTestToOneEntity::class);

        $this->assertEquals(1, count($entityMap->getOneToManyMappings()));

        $map = new OneToManyMap('users', AnnotationConfigurationTestEntity::class, 'user_id', Fetch::LAZY, new \ReflectionProperty(AnnotationConfigurationTestToOneEntity::class, 'users'));
        $this->assertEquals($map, $entityMap->getOneToManyMappings()[0]);
    }
}

#[Entity('users')]
class AnnotationConfigurationTestEntity
{
    #[Id]
    public $id;
    #[Column('user_name')]
    public $username;
    #[Column]
    public float $float;
    #[Column]
    public string $string;
    #[Column]
    public bool $isActive;
    #[Column]
    public int $rank;
    #[Column]
    public ?int $rank2;
    #[Column]
    public \DateTime $date;
}

#[Entity('posts')]
class AnnotationConfigurationTestToOneEntity
{
    #[Id]
    public $id;

    #[OneToOne]
    public AnnotationConfigurationTestEntity $user;

    #[OneToMany(AnnotationConfigurationTestEntity::class, column: 'user_id', fetchType: Fetch::LAZY)]
    public EntityCollection $users;
}
