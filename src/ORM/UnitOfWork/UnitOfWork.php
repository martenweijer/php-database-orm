<?php

namespace Electronics\Database\ORM\UnitOfWork;

class UnitOfWork
{
    protected array $identityMap = [];

    public function register(string $class, object $entity, string|int|float $identifier): void
    {
        if (!isset($this->identityMap[$class])) {
            $this->identityMap[$class] = [];
        }

        $this->identityMap[$class][$identifier] = $entity;
    }

    public function has(string $class, string|int|float $identifier): bool
    {
        return isset($this->identityMap[$class][$identifier]);
    }

    public function get(string $class, string|int|float $identifier): object
    {
        return $this->identityMap[$class][$identifier];
    }
}