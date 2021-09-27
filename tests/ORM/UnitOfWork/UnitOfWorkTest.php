<?php

namespace Electronics\Database\ORM\UnitOfWork;

use PHPUnit\Framework\TestCase;

class UnitOfWorkTest extends TestCase
{
    function testRegister(): void
    {
        $uow = new UnitOfWork();
        $uow->register(UnitOfWork::class, $uow, 1);

        $this->assertTrue($uow->has(UnitOfWork::class, 1));
        $this->assertFalse($uow->has(UnitOfWork::class, 2));
        $this->assertEquals($uow, $uow->get(UnitOfWork::class, 1));
    }
}
