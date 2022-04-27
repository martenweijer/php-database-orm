<?php

namespace Electronics\Database\ORM\Proxy;

use Electronics\Database\ORM\Mappings\EntityMap;

class DummyProxyFactory implements ProxyFactory
{
    public function createProxy(EntityMap $entityMap, float|int|string $identifier, callable $callable): object
    {
        /** @var object $object */
        $object = call_user_func($callable);
        return $object;
    }
}