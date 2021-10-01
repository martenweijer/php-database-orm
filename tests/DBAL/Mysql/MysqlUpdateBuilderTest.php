<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\Constraints\Equals;
use PHPUnit\Framework\TestCase;

class MysqlUpdateBuilderTest extends TestCase
{
    function testUpdate(): void
    {
        $builder = new MysqlUpdateBuilder('users');
        $builder->set('username', 'foo')
            ->set('password', 'bar')
            ->set('email', 'foo@bar.com');
        $this->assertEquals('update `users` set `username` = :param_0, `password` = :param_1, `email` = :param_2', $builder->generateSql());
    }

    function testConstraints(): void
    {
        $builder = new MysqlUpdateBuilder('users');
        $builder->set('username', 'foo')
            ->set('password', 'bar')
            ->set('email', 'foo@bar.com')
            ->addConstraint(new Equals('username', 'test'));
        $this->assertEquals('update `users` set `username` = :param_0, `password` = :param_1, `email` = :param_2 where `username` = :param_3', $builder->generateSql());
    }
}
