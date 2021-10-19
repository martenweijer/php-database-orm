<?php

namespace Electronics\Database\ORM\UnitOfWork;

use PHPUnit\Framework\TestCase;

class UnitOfWorkTest extends TestCase
{
    function testAddEntityToIdentityMap(): void
    {
        $uow = new UnitOfWork();
        $uow->addEntityToIdentityMap(UnitOfWork::class, $uow, 1);

        $this->assertTrue($uow->isEntityAddedToIdentityMap(UnitOfWork::class, 1));
        $this->assertFalse($uow->isEntityAddedToIdentityMap(UnitOfWork::class, 2));
        $this->assertEquals($uow, $uow->getEntityFromIdentityMap(UnitOfWork::class, 1));

        $this->assertEquals(1, count($uow->getEntities()));
    }

    function testRegister(): void
    {
        $entity = new UnitOfWork();

        $uow = new UnitOfWork();
        $uow->register($entity);
        $this->assertEquals(1, count($uow->getEntities()));
        $this->assertFalse($uow->isRemoved($entity));
    }

    function testSnapshot(): void
    {
        $entity = new UnitOfWork();

        $uow = new UnitOfWork();
        $uow->register($entity);

        $this->assertEquals($entity, $uow->getSnapshot($entity));

        $entity->prop = true;
        $this->assertNotEquals($entity, $uow->getSnapshot($entity));
    }

    function testDelete(): void
    {
        $entity = new UnitOfWork();

        $uow = new UnitOfWork();
        $uow->delete($entity);

        $this->assertTrue($uow->isRemoved($entity));
    }
}
