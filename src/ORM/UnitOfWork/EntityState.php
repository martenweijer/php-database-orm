<?php

namespace Electronics\Database\ORM\UnitOfWork;

class EntityState
{
    private object $entity;
    private State $state;

    public function __construct(object $entity, State $state)
    {
        $this->entity = $entity;
        $this->state = $state;
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function getState(): State
    {
        return $this->state;
    }
}