<?php

namespace Electronics\Database\ORM\Persisting;

use Electronics\Database\Connections\Connection;
use Electronics\Database\DBAL\Constraints\Equals;
use Electronics\Database\ORM\Configurations\Configuration;
use Electronics\Database\ORM\Mappings\PropertyMap;
use Electronics\Database\ORM\Typings\ValueConverter;
use Electronics\Database\ORM\UnitOfWork\EntityRegistry;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;

class EntityPersister implements Persister
{
    protected Connection $connection;
    protected Configuration $configuration;
    protected ValueConverter $valueConverter;
    protected UnitOfWork $unitOfWork;
    protected EntityRegistry $entityRegistry;

    public function __construct(Connection $connection, Configuration $configuration, ValueConverter $valueConverter, UnitOfWork $unitOfWork, EntityRegistry $entityRegistry)
    {
        $this->connection = $connection;
        $this->configuration = $configuration;
        $this->valueConverter = $valueConverter;
        $this->unitOfWork = $unitOfWork;
        $this->entityRegistry = $entityRegistry;
    }

    public function insert(object $entity): void
    {
        $entityMap = $this->configuration->retrieveEntityMap($entity);
        $builder = $this->connection->getBuilderFactory()->createInsertBuilder($entityMap->getTable());

        foreach ($entityMap->getProperties() as $propertyMap) {
            /** @var PropertyMap $propertyMap */
            $builder->add(
                $propertyMap->getColumn(),
                $this->valueConverter->convertToSqlValue($propertyMap->getValue($entity), $propertyMap)
            );
        }

        $this->connection->execute($builder);

        $identifier = $this->connection->retrieveLastInsertId();
        $identity = $entityMap->getIdentity();
        $identity->setValue($entity, $this->valueConverter->convertToSqlValue($identifier, $identity));

        $this->unitOfWork->register($entityMap->getClass(), $entity, $identifier);
        $this->entityRegistry->add($entity);
    }

    public function update(object $entity): void
    {
        $entityMap = $this->configuration->retrieveEntityMap($entity);
        $builder = $this->connection->getBuilderFactory()->createUpdateBuilder($entityMap->getTable());

        $identity = $entityMap->getIdentity();
        $builder->addConstraint(new Equals($identity->getColumn(), $identity->getValue($entity)));

        foreach ($entityMap->getProperties() as $propertyMap) {
            /** @var PropertyMap $propertyMap */
            $builder->set(
                $propertyMap->getColumn(),
                $this->valueConverter->convertToSqlValue($propertyMap->getValue($entity), $propertyMap)
            );
        }

        $this->connection->execute($builder);
    }

    public function delete(object $entity): void
    {
        $entityMap = $this->configuration->retrieveEntityMap($entity);
        $builder = $this->connection->getBuilderFactory()->createDeleteBuilder($entityMap->getTable());

        $identity = $entityMap->getIdentity();
        $builder->addConstraint(new Equals($identity->getColumn(), $identity->getValue($entity)));

        $this->connection->execute($builder);
    }
}