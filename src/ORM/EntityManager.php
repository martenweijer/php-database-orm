<?php

namespace Electronics\Database\ORM;

use Electronics\Database\ORM\Repositories\Repository;

interface EntityManager
{
    function find(string $entityClass, mixed $identifier): object;
    function load(string $entityClass): Repository;
}