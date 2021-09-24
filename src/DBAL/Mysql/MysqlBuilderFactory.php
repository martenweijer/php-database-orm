<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\DBAL\SelectBuilder;

class MysqlBuilderFactory implements BuilderFactory
{
    public function createSelectBuilder(string $table): SelectBuilder
    {
        return new MysqlSelectBuilder($table);
    }
}