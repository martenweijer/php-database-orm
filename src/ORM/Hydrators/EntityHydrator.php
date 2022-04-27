<?php

namespace Electronics\Database\ORM\Hydrators;

use Electronics\Database\ORM\Collections\EntityCollection;
use Electronics\Database\ORM\Collections\ProxyCollection;
use Electronics\Database\ORM\Configurations\Configuration;
use Electronics\Database\ORM\EntityManager;
use Electronics\Database\ORM\Exceptions\EntityNotFoundException;
use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Mappings\OneToManyMap;
use Electronics\Database\ORM\Mappings\OneToOneMap;
use Electronics\Database\ORM\Mappings\PropertyMap;
use Electronics\Database\ORM\Proxy\ProxyFactory;
use Electronics\Database\ORM\Typings\Fetch;
use Electronics\Database\ORM\Typings\ValueConverter;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;

class EntityHydrator implements Hydrator
{
    protected Configuration $configuration;
    protected ValueConverter $valueConverter;
    protected UnitOfWork $unitOfWork;
    protected ProxyFactory $proxyFactory;

    public function __construct(Configuration $configuration, ValueConverter $valueConverter, UnitOfWork $unitOfWork, ProxyFactory $proxyFactory)
    {
        $this->configuration = $configuration;
        $this->valueConverter = $valueConverter;
        $this->unitOfWork = $unitOfWork;
        $this->proxyFactory = $proxyFactory;
    }

    public function hydrate(array $row, EntityMap $entityMap, EntityManager $entityManager): object
    {
        $entity = $this->retrieveEntity($row, $entityMap);

        $this->doHydrate($row, $entityMap, $entity, $entityManager);

        return $entity;
    }

    protected function doHydrate(array $row, EntityMap $entityMap, object $entity, EntityManager $entityManager): void
    {
        foreach ($entityMap->getProperties() as $propertyMap) {
            if (!array_key_exists($column = $propertyMap->getColumn(), $row)) {
                throw new \InvalidArgumentException(sprintf('Property "%s" (column: "%s") not found in resultset for entity "%s".',
                    $propertyMap->getName(),
                    $propertyMap->getColumn(),
                    $entityMap->getClass()));
            }

            /** @var string|float|int|bool|null $value */
            $value = $row[$column];
            $propertyMap->setValue($entity, $this->valueConverter->convertFromSqlValue($value, $propertyMap));
        }

        foreach ($entityMap->getOneToOneMappings() as $oneMapping) {
            if (!array_key_exists($column = $oneMapping->getColumn(), $row)) {
                continue;
            }

            /** @var float|int|string|null $identifier */
            $identifier = $row[$column];

            if ($identifier !== null) {
                if ($this->unitOfWork->isEntityAddedToIdentityMap($oneMapping->getTargetClass(), $identifier)) {
                    $oneMapping->setValue($entity, $this->unitOfWork->getEntityFromIdentityMap($oneMapping->getTargetClass(), $identifier));
                    continue;
                }

                $targetEntityMap = $this->configuration->retrieveEntityMap($oneMapping->getTargetClass());

                $callable = function() use($targetEntityMap, $entityManager, $oneMapping, $identifier): object {
                    $entities = $entityManager->load($oneMapping->getTargetClass())
                        ->findBy([
                            $targetEntityMap->getIdentity()->getColumn() => $identifier
                        ]);

                    if (count($entities) !== 1) {
                        throw new EntityNotFoundException();
                    }

                    /** @var object[] $entities */
                    return $entities[0];
                };

                if ($oneMapping->getFetchType() === Fetch::LAZY) {
                    $targetEntity = $this->proxyFactory->createProxy(
                        $targetEntityMap,
                        $identifier,
                        $callable
                    );
                } else {
                    $targetEntity = $entityManager->find($oneMapping->getTargetClass(), $identifier);
                }

                $oneMapping->setValue($entity, $targetEntity);

                $this->unitOfWork->addEntityToIdentityMap($oneMapping->getTargetClass(), $targetEntity, $identifier);
            }
        }

        foreach ($entityMap->getOneToManyMappings() as $manyMapping) {
            $targetEntityMap = $this->configuration->retrieveEntityMap($manyMapping->getTargetClass());

            $callable = function(EntityCollection $collection) use($targetEntityMap, $entityManager, $manyMapping, $entityMap, $entity): void {
                $entities = $entityManager->load($targetEntityMap->getClass())
                    ->findBy([
                        $manyMapping->getColumn() => $entityMap->getIdentity()->getValue($entity)
                    ]);

                $collection->setEntities($entities);
            };

            $collection = new ProxyCollection($callable);
            $manyMapping->setValue($entity, $collection);
        }
    }

    protected function retrieveEntity(array $row, EntityMap $entityMap): object
    {
        /** @var string|int|float $identifier */
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