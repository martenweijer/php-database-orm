<?php

namespace Electronics\Database\ORM\Hydrators;

use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Mappings\PropertyMap;
use Electronics\Database\ORM\Typings\ValueConverter;
use Electronics\Database\ORM\UnitOfWork\EntityRegistry;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;

class EntityHydrator implements Hydrator
{
    protected ValueConverter $valueConverter;
    protected UnitOfWork $unitOfWork;
    protected EntityRegistry $entityRegistry;

    public function __construct(ValueConverter $valueConverter, UnitOfWork $unitOfWork, EntityRegistry $entityRegistry)
    {
        $this->valueConverter = $valueConverter;
        $this->unitOfWork = $unitOfWork;
        $this->entityRegistry = $entityRegistry;
    }

    public function hydrate(array $row, EntityMap $entityMap): object
    {
        $entity = $this->retrieveEntity($row, $entityMap);

        $this->doHydrate($row, $entityMap, $entity);

        return $entity;
    }

    protected function doHydrate(array $row, EntityMap $entityMap, $entity): void
    {
        foreach ($entityMap->getProperties() as $propertyMap) {
            /** @var PropertyMap $propertyMap */
            if (!array_key_exists($column = $propertyMap->getColumn(), $row)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" (column: "%s") not found in resultset for entity "%s".',
                    $propertyMap->getName(),
                    $propertyMap->getColumn(),
                    $entityMap->getClass()));
            }

            $propertyMap->setValue($entity, $this->valueConverter->convertFromSqlValue($row[$column], $propertyMap));
        }
    }

    protected function retrieveEntity(array $row, EntityMap $entityMap): object
    {
        $identifier = $row[$entityMap->getIdentity()->getColumn()];

        if ($this->unitOfWork->has($entityMap->getClass(), $identifier)) {
            return $this->unitOfWork->get($entityMap->getClass(), $identifier);
        }

        $entity = $entityMap->newInstance();
        $entityMap->getIdentity()->setValue($entity, $identifier);

        $this->unitOfWork->register($entityMap->getClass(), $entity, $identifier);
        $this->entityRegistry->add($entity);

        return $entity;
    }
}