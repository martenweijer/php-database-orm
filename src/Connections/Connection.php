<?php

namespace Electronics\Database\Connections;

use Electronics\Database\DBAL\Builder;
use Electronics\Database\DBAL\BuilderFactory;

interface Connection
{
    function execute(Builder $builder): \PDOStatement;
    function getBuilderFactory(): BuilderFactory;
}