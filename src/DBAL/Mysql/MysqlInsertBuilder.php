<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\Constraints\SimpleParameterFactory;
use Electronics\Database\DBAL\InsertBuilder;

class MysqlInsertBuilder implements InsertBuilder
{
    protected SimpleParameterFactory $parameterFactory;

    protected string $table;
    protected array $values = [];

    public function __construct(string $table)
    {
        $this->table = $table;

        $this->parameterFactory = new SimpleParameterFactory();
    }

    public function generateSql(): string
    {
        $keys = implode(', ', array_keys($this->values));
        $values = implode(', ', array_values($this->values));

        return "insert into $this->table ($keys) values ($values)";
    }

    public function getParameters(): array
    {
        return $this->parameterFactory->getParameters();
    }

    public function add(string $column, float|int|string $value): static
    {
        $this->values[$column] = $this->parameterFactory->generateParameter($value);
        return $this;
    }
}