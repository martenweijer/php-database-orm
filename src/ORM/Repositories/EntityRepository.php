<?php

namespace Electronics\Database\ORM\Repositories;

use Electronics\Database\DBAL\Constraints\Equals;
use Electronics\Database\DBAL\SelectBuilder;
use Electronics\Database\DBAL\Types\OrderType;
use Electronics\Database\ORM\DatabaseContext;
use Electronics\Database\ORM\Exceptions\EntityNotFoundException;
use Electronics\Database\ORM\Mappings\EntityMap;

class EntityRepository implements Repository
{
    protected EntityMap $entityMap;
    protected DatabaseContext $databaseContext;

    public function __construct(string $entityClass, DatabaseContext $databaseContext)
    {
        $this->entityMap = $databaseContext->getConfiguration()->retrieveEntityMap($entityClass);
        $this->databaseContext = $databaseContext;
    }

    public function find(string|int|float $identifier): object
    {
        $entities = $this->findBy([
            $this->entityMap->getIdentity()->getColumn() => $identifier
        ], 1);

        if (count($entities) !== 1) {
            throw new EntityNotFoundException();
        }

        return $entities[0];
    }

    public function findBy(array $criteria = [], ?int $limit = null, array $orderBy = []): array
    {
        $builder = $this->createBuilder();

        if ($limit) {
            $builder->setLimit($limit);
        }

        foreach ($orderBy as $column => $direction) {
            $builder->orderBy($column, OrderType::orderTypeFromString($direction));
        }

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
            $entities[] = $this->databaseContext->getHydrator()->hydrate($row, $this->entityMap);
        }

        return $entities;
    }
}