<?php

namespace Electronics\Database\ORM\Persisting;

use Electronics\Database\Connections\Connection;
use Electronics\Database\DBAL\Constraints\Equals;
use Electronics\Database\ORM\Configurations\Configuration;
use Electronics\Database\ORM\Mappings\OneToOneMap;
use Electronics\Database\ORM\Mappings\PropertyMap;
use Electronics\Database\ORM\Typings\ValueConverter;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;

class EntityPersister implements Persister
{
    protected Connection $connection;
    protected Configuration $configuration;
    protected ValueConverter $valueConverter;
    protected UnitOfWork $unitOfWork;

    public function __construct(Connection $connection, Configuration $configuration, ValueConverter $valueConverter, UnitOfWork $unitOfWork)
    {
        $this->connection = $connection;
        $this->configuration = $configuration;
        $this->valueConverter = $valueConverter;
        $this->unitOfWork = $unitOfWork;
    }

    public function insert(object $entity): void
    {
        $entityMap = $this->configuration->retrieveEntityMap($entity);
        $builder = $this->connection->getBuilderFactory()->createInsertBuilder($entityMap->getTable());

        foreach ($entityMap->getProperties() as $propertyMap) {
            $builder->add(
                $propertyMap->getColumn(),
                $this->valueConverter->convertToSqlValue($propertyMap->getValue($entity), $propertyMap)
            );
        }

        foreach ($entityMap->getOneToOneMappings() as $oneMapping) {
            $relationEntity = $oneMapping->getValue($entity);

            if ($relationEntity === null) {
                continue;
            }

            /** @var string|int|float $relationEntityId */
            $relationEntityId = $this->configuration->retrieveEntityMap($oneMapping->getTargetClass())
                ->getIdentity()->getValue($relationEntity);
            $builder->add($oneMapping->getColumn(), $relationEntityId);
        }

        $this->connection->execute($builder);

        $identifier = $this->connection->retrieveLastInsertId();
        $identity = $entityMap->getIdentity();
        $identity->setValue($entity, $this->valueConverter->convertToSqlValue($identifier, $identity));

        $this->unitOfWork->addEntityToIdentityMap($entityMap->getClass(), $entity, $identifier);
    }

    public function update(object $entity): void
    {
        $entityMap = $this->configuration->retrieveEntityMap($entity);
        $builder = $this->connection->getBuilderFactory()->createUpdateBuilder($entityMap->getTable());

        $identity = $entityMap->getIdentity();
        /** @var float|int|string $identifier */
        $identifier = $identity->getValue($entity);
        $builder->addConstraint(new Equals($identity->getColumn(), $identifier));

        foreach ($entityMap->getProperties() as $propertyMap) {
            $builder->set(
                $propertyMap->getColumn(),
                $this->valueConverter->convertToSqlValue($propertyMap->getValue($entity), $propertyMap)
            );
        }

        foreach ($entityMap->getOneToOneMappings() as $oneMapping) {
            $relationEntity = $oneMapping->getValue($entity);

            /** @var string|int|float|null $relationEntityId */
            $relationEntityId = $relationEntity === null ? null : $this->configuration->retrieveEntityMap($oneMapping->getTargetClass())
                ->getIdentity()->getValue($relationEntity);
            $builder->set($oneMapping->getColumn(), $relationEntityId);
        }

        $this->connection->execute($builder);
    }

    public function delete(object $entity): void
    {
        $entityMap = $this->configuration->retrieveEntityMap($entity);
        $builder = $this->connection->getBuilderFactory()->createDeleteBuilder($entityMap->getTable());

        $identity = $entityMap->getIdentity();
        /** @var float|int|string $identifier */
        $identifier = $identity->getValue($entity);
        $builder->addConstraint(new Equals($identity->getColumn(), $identifier));

        $this->connection->execute($builder);
    }
}