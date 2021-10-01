<?php

namespace Electronics\Database\DBAL\Mysql;

use Electronics\Database\DBAL\Constraints\Constraint;
use Electronics\Database\DBAL\Constraints\SimpleParameterFactory;
use Electronics\Database\DBAL\Types\OrderType;
use Electronics\Database\DBAL\SelectBuilder;

class MysqlSelectBuilder implements SelectBuilder
{
    protected SimpleParameterFactory $parameterFactory;

    protected string $table;
    protected ?int $limit = null;
    protected array $whereConstraints = [];
    protected array $orderBy = [];

    public function __construct(string $table)
    {
        $this->table = $table;

        $this->parameterFactory = new SimpleParameterFactory();
    }

    public function generateSql(): string
    {
        $sql = "select * from `$this->table`";

        if ($this->whereConstraints) {
            $sql .= ' where '. implode($this->whereConstraints);
        }

        if ($this->orderBy) {
            $sql .= ' order by '. implode(', ', $this->orderBy);
        }

        if ($this->limit) {
            $sql .= ' limit '. $this->limit;
        }

        return $sql;
    }

    public function getParameters(): array
    {
        return $this->parameterFactory->getParameters();
    }

    public function setLimit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    public function addConstraint(Constraint $constraint): static
    {
        $this->whereConstraints[] = $constraint->generateSql($this->parameterFactory);
        return $this;
    }

    public function orderBy(string $column, OrderType $orderType): static
    {
        $this->orderBy[] = "`$column`" . match ($orderType) {
            OrderType::ASC => ' asc',
            OrderType::DESC => ' desc',
        };

        return $this;
    }
}