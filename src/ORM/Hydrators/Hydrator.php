<?php

namespace Electronics\Database\ORM\Hydrators;

use Electronics\Database\ORM\EntityManager;
use Electronics\Database\ORM\Mappings\EntityMap;

interface Hydrator
{
    function hydrate(array $row, EntityMap $entityMap, EntityManager $entityManager): object;
}