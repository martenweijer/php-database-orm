<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\Constraints\Equals;
use Electronics\Database\DBAL\Constraints\In;
use Electronics\Database\DBAL\Types\OrderType;
use PHPUnit\Framework\TestCase;

class MysqlSelectBuilderTest extends TestCase
{
    function testSelect(): void
    {
        $builder = new MysqlSelectBuilder('users');
        $this->assertEquals('select * from `users`', $builder->generateSql());
    }

    function testConstraint(): void
    {
        $builder = new MysqlSelectBuilder('users');
        $builder->addConstraint(new Equals('id', 1));
        $this->assertEquals('select * from `users` where `id` = :param_0', $builder->generateSql());
        $this->assertEquals([':param_0' => 1], $builder->getParameters());
    }

    function testInConstraint(): void
    {
        $builder = new MysqlSelectBuilder('users');
        $builder->addConstraint(new In('id', [1, 2, 3]));
        $this->assertEquals('select * from `users` where `id` in (:param_0, :param_1, :param_2)', $builder->generateSql());
        $this->assertEquals([
            ':param_0' => 1,
            ':param_1' => 2,
            ':param_2' => 3,
        ], $builder->getParameters());
    }

    function testLimit(): void
    {
        $builder = new MysqlSelectBuilder('users');
        $builder->setLimit(10);
        $this->assertEquals('select * from `users` limit 10', $builder->generateSql());
    }

    function testOrderBy(): void
    {
        $builder = new MysqlSelectBuilder('users');
        $builder->orderBy('username', orderType: OrderType::ASC)
            ->orderBy('password', orderType: OrderType::DESC);
        $this->assertEquals('select * from `users` order by `username` asc, `password` desc', $builder->generateSql());
    }
}
