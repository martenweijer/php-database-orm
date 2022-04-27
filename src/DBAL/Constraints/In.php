<?php

namespace Electronics\Database\DBAL\Constraints;

class In implements Constraint
{
    protected string $column;
    /** @var array<float|int|null|string> */
    protected array $values;

    public function __construct(string $column, array $values)
    {
        $this->column = $column;
        /** @var array<float|int|null|string> $values */
        $this->values = $values;
    }

    public function generateSql(ParameterFactory $factory): string
    {
        $sql = "`$this->column` in (";
        $values = [];
        foreach ($this->values as $value) {
            $values[] = $factory->generateParameter($value);
        }
        $sql .= implode(', ', $values);
        $sql .= ')';
        return $sql;
    }
}