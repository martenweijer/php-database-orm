<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\Constraints\Constraint;
use Electronics\Database\DBAL\Constraints\SimpleParameterFactory;
use Electronics\Database\DBAL\UpdateBuilder;

class MysqlUpdateBuilder implements UpdateBuilder
{
    protected SimpleParameterFactory $parameterFactory;

    protected string $table;
    protected array $whereConstraints = [];
    /** @var scalar[] */
    protected array $values = [];

    public function __construct(string $table)
    {
        $this->table = $table;

        $this->parameterFactory = new SimpleParameterFactory();
    }

    public function generateSql(): string
    {
        $sql = "update `$this->table`";

        $sql .= ' set '. implode(', ', $this->values);

        if ($this->whereConstraints) {
            $sql .= ' where '. implode($this->whereConstraints);
        }

        return $sql;
    }

    public function getParameters(): array
    {
        return $this->parameterFactory->getParameters();
    }

    public function addConstraint(Constraint $constraint): static
    {
        $this->whereConstraints[] = $constraint->generateSql($this->parameterFactory);
        return $this;
    }

    public function set(string $column, string|int|float|null $value): static
    {
        $this->values[] = "`$column` = ". $this->parameterFactory->generateParameter($value);
        return $this;
    }
}