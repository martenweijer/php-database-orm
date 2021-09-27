<?php

namespace Electronics\Database\ORM\Repositories;

interface Repository
{
    function find(string|int|float $identifier): object;
    function findBy(array $criteria = [], ?int $limit = null, array $orderBy = []);
    function findAll(): array;
}