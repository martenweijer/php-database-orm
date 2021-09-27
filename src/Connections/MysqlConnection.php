<?php

namespace Electronics\Database\Connections;

use Electronics\Database\DBAL\Builder;
use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\DBAL\Mysql\MysqlBuilderFactory;
use PDO;

class MysqlConnection extends PDO implements Connection
{
    protected string $database;

    protected BuilderFactory $builderFactory;

    public function __construct(string $host, string $database, string $username, string $password)
    {
        $this->database = $database;

        $this->builderFactory = new MysqlBuilderFactory();

        parent::__construct(sprintf('mysql:host=%s;dbname=%s;charset=utf8;', $host, $database), $username, $password);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public function execute(Builder $builder): \PDOStatement
    {
        $statement = $this->prepare($builder->generateSql());
        $statement->execute($builder->getParameters());
        return $statement;
    }

    public function getBuilderFactory(): BuilderFactory
    {
        return $this->builderFactory;
    }
}