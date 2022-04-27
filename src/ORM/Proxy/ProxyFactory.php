<?php

namespace Electronics\Database\ORM\Proxy;

use Electronics\Database\ORM\Mappings\EntityMap;

interface ProxyFactory
{
    function createProxy(EntityMap $entityMap, float|int|string $identifier, callable $callable): object;
}