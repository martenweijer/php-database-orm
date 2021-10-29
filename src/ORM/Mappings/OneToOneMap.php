<?php

namespace Electronics\Database\ORM\Mappings;

class OneToOneMap
{
    protected string $name;
    protected string $targetClass;
    protected string $column;
    protected \ReflectionProperty $reflectionProperty;

    public function __construct(string $name, string $targetClass, string $column, \ReflectionProperty $reflectionProperty)
    {
        $this->name = $name;
        $this->targetClass = $targetClass;
        $this->column = $column;
        $this->reflectionProperty = $reflectionProperty;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTargetClass(): string
    {
        return $this->targetClass;
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function setValue(object $entity, object $value): void
    {
        $this->reflectionProperty->setValue($entity, $value);
    }

    public function getValue(object $entity): object|null
    {
        if (!$this->reflectionProperty->isInitialized($entity)) {
            return null;
        }

        return $this->reflectionProperty->getValue($entity);
    }
}