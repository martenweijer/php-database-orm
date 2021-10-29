<?php

namespace Electronics\Database\ORM\Hydrators;

use Electronics\Database\ORM\EntityManager;
use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Mappings\OneToOneMap;
use Electronics\Database\ORM\Mappings\PropertyMap;
use Electronics\Database\ORM\Typings\ValueConverter;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;

class EntityHydrator implements Hydrator
{
    protected ValueConverter $valueConverter;
    protected UnitOfWork $unitOfWork;

    public function __construct(ValueConverter $valueConverter, UnitOfWork $unitOfWork)
    {
        $this->valueConverter = $valueConverter;
        $this->unitOfWork = $unitOfWork;
    }

    public function hydrate(array $row, EntityMap $entityMap, EntityManager $entityManager): object
    {
        $entity = $this->retrieveEntity($row, $entityMap);

        $this->doHydrate($row, $entityMap, $entity, $entityManager);

        return $entity;
    }

    protected function doHydrate(array $row, EntityMap $entityMap, $entity, EntityManager $entityManager): void
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

        foreach ($entityMap->getOneToOneMappings() as $oneMapping) {
            /** @var OneToOneMap $oneMapping */
            if (!array_key_exists($column = $oneMapping->getColumn(), $row)) {
                continue;
            }

            $identity = $row[$column];

            if ($identity !== null) {
                $targetEntity = $entityManager->find($oneMapping->getTargetClass(), $row[$column]);
                $oneMapping->setValue($entity, $targetEntity);
            }
        }
    }

    protected function retrieveEntity(array $row, EntityMap $entityMap): object
    {
        $identifier = $row[$entityMap->getIdentity()->getColumn()];

        if ($this->unitOfWork->isEntityAddedToIdentityMap($entityMap->getClass(), $identifier)) {
            return $this->unitOfWork->getEntityFromIdentityMap($entityMap->getClass(), $identifier);
        }

        $entity = $entityMap->newInstance();
        $entityMap->getIdentity()->setValue($entity, $identifier);

        $this->unitOfWork->addEntityToIdentityMap($entityMap->getClass(), $entity, $identifier);

        return $entity;
    }
}