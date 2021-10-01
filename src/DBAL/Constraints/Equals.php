<?php

namespace Electronics\Database\DBAL\Constraints;

class Equals implements Constraint
{
    protected string $column;
    protected string|int|float $value;

    public function __construct(string $column, float|int|string $value)
    {
        $this->column = $column;
        $this->value = $value;
    }

    public function generateSql(ParameterFactory $factory): string
    {
        return "`$this->column` = ". $factory->generateParameter($this->value);
    }
}