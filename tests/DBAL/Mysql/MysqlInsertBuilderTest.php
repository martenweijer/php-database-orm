<?php

namespace Electronics\Database\DBAL\Mysql;

use PHPUnit\Framework\TestCase;

class MysqlInsertBuilderTest extends TestCase
{
    function testInsert(): void
    {
        $builder = new MysqlInsertBuilder('users');
        $builder->add('username', 'foo')
            ->add('password', 'bar')
            ->add('email', 'test@test.com');
        $this->assertEquals('insert into users (username, password, email) values (:param_0, :param_1, :param_2)', $builder->generateSql());
    }
}
