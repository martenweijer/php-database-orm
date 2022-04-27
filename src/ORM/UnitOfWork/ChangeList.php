<?php

namespace Electronics\Database\ORM\UnitOfWork;

use Electronics\Database\ORM\Configurations\Configuration;
use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Mappings\PropertyMap;

class ChangeList
{
    protected Configuration $configuration;
    protected UnitOfWork $unitOfWork;

    public function __construct(Configuration $configuration, UnitOfWork $unitOfWork)
    {
        $this->configuration = $configuration;
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * @return EntityState[]
     */
    public function determineChanges(): array
    {
        $entityStates = [];

        foreach ($this->unitOfWork->getEntities() as $entity) {
            $state = $this->determineState($entity);
            $entityStates[] = new EntityState($entity, $state);
        }

        return $entityStates;
    }

    protected function determineState(object $entity): State
    {
        if ($this->unitOfWork->isRemoved($entity)) {
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
        $snapshot = $this->unitOfWork->getSnapshot($entity);

        foreach ($entityMap->getProperties() as $propertyMap) {
            if ($propertyMap->getValue($entity) !== $propertyMap->getValue($snapshot)) {
                return true;
            }
        }

        return false;
    }
}