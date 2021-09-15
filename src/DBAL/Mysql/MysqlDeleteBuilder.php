<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\Constraints\Constraint;
use Electronics\Database\DBAL\Constraints\SimpleParameterFactory;
use Electronics\Database\DBAL\DeleteBuilder;

class MysqlDeleteBuilder implements DeleteBuilder
{
    protected SimpleParameterFactory $parameterFactory;

    protected string $table;
    protected array $whereConstraints = [];

    public function __construct(string $table)
    {
        $this->table = $table;

        $this->parameterFactory = new SimpleParameterFactory();
    }

    public function generateSql(): string
    {
        $sql = 'delete from '. $this->table;

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
}