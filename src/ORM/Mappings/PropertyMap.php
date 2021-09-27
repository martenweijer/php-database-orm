<?php

namespace Electronics\Database\ORM\Mappings;

use Electronics\Database\ORM\Typings\ColumnType;

class PropertyMap
{
    protected string $name;
    protected string $column;
    protected ColumnType $columnType;
    protected \ReflectionProperty $reflectionProperty;

    public function __construct(string $name, string $column, ColumnType $columnType, \ReflectionProperty $reflectionProperty)
    {
        $this->name = $name;
        $this->column = $column;
        $this->columnType = $columnType;
        $this->reflectionProperty = $reflectionProperty;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getColumnType(): ColumnType
    {
        return $this->columnType;
    }

    public function setValue($entity, mixed $value): void
    {
        $this->reflectionProperty->setValue($entity, $value);
    }
}