<?php

namespace Electronics\Database\ORM;

use Electronics\Database\ORM\Repositories\EntityRepository;
use Electronics\Database\ORM\Repositories\Repository;
use Electronics\Database\ORM\UnitOfWork\ChangeList;
use Electronics\Database\ORM\UnitOfWork\EntityState;
use Electronics\Database\ORM\UnitOfWork\State;

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

    public function add(object $entity): void
    {
        $this->databaseContext->getUnitOfWork()->register($entity);
    }

    public function delete(object $entity): void
    {
        $this->databaseContext->getUnitOfWork()->delete($entity);
    }

    public function save(object $entity = null): void
    {
        if ($entity) {
            $this->add($entity);
        }

        $this->commit();
    }

    protected function commit(): void
    {
        $changeList = new ChangeList(
            $this->databaseContext->getConfiguration(),
            $this->databaseContext->getUnitOfWork()
        );
        $entityStates = $changeList->determineChanges();

        foreach ($entityStates as $entityState) {
            /** @var EntityState $entityState */

            switch ($entityState->getState()) {
                case State::ADDED:
                    $this->databaseContext->getPersister()->insert($entityState->getEntity());
                    break;
                case State::MODIFIED:
                    $this->databaseContext->getPersister()->update($entityState->getEntity());
                    break;
                case State::DELETED:
                    $this->databaseContext->getPersister()->delete($entityState->getEntity());
                    break;
            }
        }
    }
}