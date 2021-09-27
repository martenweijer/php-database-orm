<?php

namespace Electronics\Database\ORM;

use Electronics\Database\ORM\Repositories\EntityRepository;
use Electronics\Database\ORM\Repositories\Repository;

class SimpleEntityManager implements EntityManager
{
    protected DatabaseContext $databaseContext;

    public function __construct(DatabaseContext $databaseContext)
    {
        $this->databaseContext = $databaseContext;
    }

    public function find(string $entityClass, mixed $identifier): object
    {
        return $this->load($entityClass)
            ->find($identifier);
    }

    public function load(string $entityClass): Repository
    {
        return new EntityRepository(
            $entityClass,
            $this->databaseContext
        );
    }
}