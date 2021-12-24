<?php

namespace Electronics\Database\ORM\Proxy;

use Electronics\Database\ORM\Mappings\EntityMap;

interface ProxyFactory
{
    function createProxy(EntityMap $entityMap, string $identifier, callable $callable): object;
}