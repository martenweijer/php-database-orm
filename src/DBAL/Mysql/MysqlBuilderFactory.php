<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\DBAL\DeleteBuilder;
use Electronics\Database\DBAL\InsertBuilder;
use Electronics\Database\DBAL\SelectBuilder;
use Electronics\Database\DBAL\UpdateBuilder;

class MysqlBuilderFactory implements BuilderFactory
{
    public function createSelectBuilder(string $table): SelectBuilder
    {
        return new MysqlSelectBuilder($table);
    }

    public function createInsertBuilder(string $table): InsertBuilder
    {
        return new MysqlInsertBuilder($table);
    }

    public function createUpdateBuilder(string $table): UpdateBuilder
    {
        return new MysqlUpdateBuilder($table);
    }

    public function createDeleteBuilder(string $table): DeleteBuilder
    {
        return new MysqlDeleteBuilder($table);
    }
}