<?php

namespace Electronics\Database\ORM\UnitOfWork;

class UnitOfWork
{
    protected array $identityMap = [];

    protected array $entities = [];
    protected array $removedEntities = [];
    protected array $snapshots = [];

    public function addEntityToIdentityMap(string $class, object $entity, string|int|float $identifier): void
    {
        if ($this->isEntityAddedToIdentityMap($class, $identifier)) {
            return;
        }

        if (!isset($this->identityMap[$class])) {
            $this->identityMap[$class] = [];
        }

        $this->identityMap[$class][$identifier] = $entity;

        $this->register($entity);
    }

    public function isEntityAddedToIdentityMap(string $class, string|int|float $identifier): bool
    {
        return isset($this->identityMap[$class][$identifier]);
    }

    public function getEntityFromIdentityMap(string $class, string|int|float $identifier): object
    {
        return $this->identityMap[$class][$identifier];
    }

    public function register(object $entity): void
    {
        $identifier = $this->determineIdentifier($entity);
        if (!isset($this->entities[$identifier])) {
            $this->entities[$identifier] = $entity;
            $this->snapshots[$identifier] = $this->createSnapshot($entity);
        }
    }

    public function delete(object $entity): void
    {
        $this->register($entity);

        $identifier = $this->determineIdentifier($entity);
        $this->removedEntities[$identifier] = $entity;
    }

    public function getEntities(): array
    {
        return $this->entities;
    }

    public function isRemoved(object $entity): bool
    {
        $identifier = $this->determineIdentifier($entity);
        return isset($this->removedEntities[$identifier]);
    }

    public function getSnapshot(object $entity): object
    {
        $identifier = $this->determineIdentifier($entity);
        return $this->snapshots[$identifier];
    }

    protected function determineIdentifier(object $entity): string
    {
        return spl_object_hash($entity);
    }

    protected function createSnapshot(object $entity): object
    {
        return clone $entity;
    }
}