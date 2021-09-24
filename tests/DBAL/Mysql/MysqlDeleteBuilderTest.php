<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\Constraints\Equals;
use PHPUnit\Framework\TestCase;

class MysqlDeleteBuilderTest extends TestCase
{
    function testDelete(): void
    {
        $builder = new MysqlDeleteBuilder('users');
        $builder->addConstraint(new Equals('id', 1));
        $this->assertEquals('delete from users where id = :param_0', $builder->generateSql());
    }
}
