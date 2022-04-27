<?php

namespace Electronics\Database\ORM\Repositories;

use Electronics\Database\DBAL\Constraints\Equals;
use Electronics\Database\DBAL\SelectBuilder;
use Electronics\Database\DBAL\Types\OrderType;
use Electronics\Database\ORM\DatabaseContext;
use Electronics\Database\ORM\EntityManager;
use Electronics\Database\ORM\Exceptions\EntityNotFoundException;
use Electronics\Database\ORM\Mappings\EntityMap;

class EntityRepository implements Repository
{
    protected EntityMap $entityMap;
    protected DatabaseContext $databaseContext;
    protected EntityManager $entityManager;

    public function __construct(string $entityClass, DatabaseContext $databaseContext, EntityManager $entityManager)
    {
        $this->entityMap = $databaseContext->getConfiguration()->retrieveEntityMap($entityClass);
        $this->databaseContext = $databaseContext;
        $this->entityManager = $entityManager;
    }

    public function find(string|int|float $identifier): object
    {
        if ($this->databaseContext->getUnitOfWork()->isEntityAddedToIdentityMap($this->entityMap->getClass(), $identifier)) {
            return $this->databaseContext->getUnitOfWork()->getEntityFromIdentityMap($this->entityMap->getClass(), $identifier);
        }

        $entities = $this->findBy([
            $this->entityMap->getIdentity()->getColumn() => $identifier
        ], 1);

        if (count($entities) !== 1) {
            throw new EntityNotFoundException();
        }

        /** @var object[] $entities */
        return $entities[0];
    }

    public function findBy(array $criteria = [], ?int $limit = null, array $orderBy = []): array
    {
        $builder = $this->createBuilder();

        if ($limit) {
            $builder->setLimit($limit);
        }

        /** @var array<string, string> $orderBy */
        foreach ($orderBy as $column => $direction) {
            $builder->orderBy($column, OrderType::orderTypeFromString($direction));
        }

        /** @var array<string, float|int|string> $criteria */
        foreach ($criteria as $column => $value) {
            $builder->addConstraint(new Equals($column, $value));
        }

        return $this->toList($builder);
    }

    public function findAll(): array
    {
        return $this->findBy([]);
    }

    protected function createBuilder(): SelectBuilder
    {
        return $this->databaseContext->getBuilderFactory()->createSelectBuilder(
            $this->entityMap->getTable()
        );
    }

    protected function toList(SelectBuilder $builder): array
    {
        $statement = $this->databaseContext->getConnection()->execute($builder);

        $entities = [];
        foreach ($statement->fetchAll() as $row) {
            assert(is_array($row));
            $entities[] = $this->databaseContext->getHydrator()->hydrate($row, $this->entityMap, $this->entityManager);
        }

        return $entities;
    }
}