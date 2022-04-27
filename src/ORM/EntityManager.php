<?php

namespace Electronics\Database\ORM;

use Electronics\Database\ORM\Repositories\Repository;

interface EntityManager
{
    function find(string $entityClass, float|int|string $identifier): object;
    function load(string $entityClass): Repository;

    function add(object $entity): void;
    function save(object $entity = null): void;
}