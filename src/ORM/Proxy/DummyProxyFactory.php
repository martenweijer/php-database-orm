<?php

namespace Electronics\Database\ORM\Proxy;

use Electronics\Database\ORM\Mappings\EntityMap;

class DummyProxyFactory implements ProxyFactory
{
    public function createProxy(EntityMap $entityMap, string $identifier, callable $callable): object
    {
        return call_user_func($callable);
    }
}