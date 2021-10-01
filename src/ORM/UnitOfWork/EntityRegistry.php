<?php

namespace Electronics\Database\ORM\UnitOfWork;

class EntityRegistry
{
    protected array $addedEntities = [];
    protected array $removedEntities = [];
    protected array $snapshots = [];

    public function add(object $entity): void
    {
        if (!$this->has($entity)) {
            $identifier = $this->determineIdentifier($entity);
            $this->addedEntities[$identifier] = $entity;
            $this->snapshots[$identifier] = $this->createSnapshot($entity);
        }
    }

    public function delete(object $entity): void
    {
        $this->add($entity);

        $identifier = $this->determineIdentifier($entity);
        $this->removedEntities[$identifier] = $entity;
    }

    public function has(object $entity): bool
    {
        $identifier = $this->determineIdentifier($entity);
        return isset($this->addedEntities[$identifier]);
    }

    public function getAddedEntities(): array
    {
        return $this->addedEntities;
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