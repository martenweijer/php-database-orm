<?php

namespace Electronics\Database\ORM\Configurations;

use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Typings\ColumnType;
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

        $this->assertEquals(2, count($entityMap->getProperties()));

        $username = $entityMap->getProperty('username');
        $this->assertEquals('username', $username->getName());
        $this->assertEquals('user_name', $username->getColumn());
        $this->assertEquals(ColumnType::STRING, $username->getColumnType());

        $rank = $entityMap->getProperty('rank');
        $this->assertEquals('rank', $rank->getName());
        $this->assertEquals('rank', $rank->getColumn());
        $this->assertEquals(ColumnType::INT, $rank->getColumnType());
    }
}

#[Entity('users')]
class AnnotationConfigurationTestEntity
{
    #[Id]
    public $id;
    #[Column('user_name')]
    public $username;
    #[Column(columnType: ColumnType::INT)]
    public $rank;
}
