<?php

namespace Electronics\Database\ORM\UnitOfWork;

use Electronics\Database\ORM\Configurations\Configuration;
use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Mappings\PropertyMap;

class ChangeList
{
    protected Configuration $configuration;
    protected EntityRegistry $entityRegistry;

    public function __construct(Configuration $configuration, EntityRegistry $entityRegistry)
    {
        $this->configuration = $configuration;
        $this->entityRegistry = $entityRegistry;
    }

    public function determineChanges(): array
    {
        $entityStates = [];

        foreach ($this->entityRegistry->getAddedEntities() as $entity) {
            $state = $this->determineState($entity);
            $entityStates[] = new EntityState($entity, $state);
        }

        return $entityStates;
    }

    protected function determineState(object $entity): State
    {
        if ($this->entityRegistry->isRemoved($entity)) {
            return State::DELETED;
        }

        $entityMap = $this->configuration->retrieveEntityMap($entity);
        $identifier = $entityMap->getIdentity()->getValue($entity);

        if ($identifier === null) {
            return State::ADDED;
        }

        if ($this->hasChanges($entityMap, $entity)) {
            return State::MODIFIED;
        }

        return State::PERSISTED;
    }

    protected function hasChanges(EntityMap $entityMap, object $entity): bool
    {
        $snapshot = $this->entityRegistry->getSnapshot($entity);

        foreach ($entityMap->getProperties() as $propertyMap) {
            /** @var PropertyMap $propertyMap */
            if ($propertyMap->getValue($entity) !== $propertyMap->getValue($snapshot)) {
                return true;
            }
        }

        return false;
    }
}