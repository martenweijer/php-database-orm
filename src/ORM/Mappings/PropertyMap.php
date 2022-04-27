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

    public function setValue(object $entity, mixed $value): void
    {
        $this->reflectionProperty->setValue($entity, $value);
    }

    public function getValue(object $entity): string|float|int|\DateTime|null
    {
        if (!$this->reflectionProperty->isInitialized($entity)) {
            return null;
        }

        /** @var string|float|int|\DateTime|null $value */
        $value = $this->reflectionProperty->getValue($entity);
        return $value;
    }
}