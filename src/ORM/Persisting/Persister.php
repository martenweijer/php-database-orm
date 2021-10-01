<?php

namespace Electronics\Database\ORM\Persisting;

interface Persister
{
    function insert(object $entity): void;
    function update(object $entity): void;
    function delete(object $entity): void;
}