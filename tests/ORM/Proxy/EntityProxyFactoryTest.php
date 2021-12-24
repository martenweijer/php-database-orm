<?php

namespace ORM\Proxy;

use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Configurations\AnnotationConfiguration;
use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Proxy\EntityProxyFactory;
use PHPUnit\Framework\TestCase;

class EntityProxyFactoryTest extends TestCase
{
    function testGenerateClassName(): void
    {
        $factory = new EntityProxyFactory('Entity_Proxy_');
        $entityMap = new EntityMap('Electronics\Database\ORM\Entity\User', 'users', null, new \ReflectionClass($this));
        $this->assertEquals('Entity_Proxy_User', $factory->generateProxyClassName($entityMap));
        $entityMap = new EntityMap('UserPost', 'users', null, new \ReflectionClass($this));
        $this->assertEquals('Entity_Proxy_UserPost', $factory->generateProxyClassName($entityMap));
    }

    function testCreateProxy(): void
    {
        $conf = new AnnotationConfiguration();
        $factory = new EntityProxyFactory();
        $entityMap = $conf->retrieveEntityMap(EntityProxyFactoryTestEntity::class);

        $entity = $factory->createProxy($entityMap, 1, function() {});
        $this->assertInstanceOf(EntityProxyFactoryTestEntity::class, $entity);
        $this->assertEquals(1, $entity->id);
        $this->assertEquals('Entity_Proxy_EntityProxyFactoryTestEntity', get_class($entity));
    }
}

#[Entity('users')]
class EntityProxyFactoryTestEntity
{
    #[Id]
    public ?int $id;

    #[Column]
    private string $username;

    public function getUsername(): string
    {
        return $this->username;
    }
}
