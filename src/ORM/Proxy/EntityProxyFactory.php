<?php

namespace Electronics\Database\ORM\Proxy;

use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Mappings\PropertyMap;

class EntityProxyFactory implements ProxyFactory
{
    protected string $prefix;

    public function __construct(string $prefix = 'Entity_Proxy_')
    {
        $this->prefix = $prefix;
    }

    public function createProxy(EntityMap $entityMap, string $identifier, callable $callable): object
    {
        $className = $this->generateProxyClassName($entityMap);
        $this->createProxyClass($entityMap, $className);

        $class = new $className();
        $entityMap->getIdentity()->setValue($class, $identifier);

        $reflectionProperty = new \ReflectionProperty($class, '__initializer');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($class, $callable);

        return $class;
    }

    public function generateProxyClassName(EntityMap $entityMap): string
    {
        $class = $entityMap->getClass();
        if (false !== $pos = strrpos($class, '\\')) {
            return $this->prefix . substr($class, $pos + 1);
        }

        return 'Entity_Proxy_'. $class;
    }

    public function createProxyClass(EntityMap $entityMap, string $className): void
    {
        $methods = '';
        foreach ($entityMap->getProperties() as $property) {
            /** @var PropertyMap $property */
            $method = 'get'. ucfirst($property->getName());

            if (!method_exists($entityMap->getClass(), $method)) {
                continue;
            }

            $returnType = $this->findReturnType(new \ReflectionMethod($entityMap->getClass(), $method));
            if ($returnType) {
                $returnType = ': '. $returnType;
            }

            $methods .= sprintf('

    public function %s()%s
    {
        $this->initialize();
        return parent::%s();
    }', $method, $returnType, $method);
        }

        $template = sprintf('<?php

class %s extends %s
{
    
    private $__initializer;
    private bool $__isInitialized = false;%s
    
    private function initialize()
    {
        if (!$this->__isInitialized) {
            $this->__isInitialized = true;

            call_user_func($this->__initializer);
        }
    }
}', $className, $entityMap->getClass(), $methods);
        eval('?>'. $template);
    }

    private function findReturnType(\ReflectionMethod $reflection): ?string
    {
        if (!$reflection->hasReturnType()) {
            return null;
        }

        $type = $reflection->getReturnType();
        if ($type instanceof \ReflectionNamedType) {
            return ($type->allowsNull() ? '?' : '') . $type->getName();
        }

        $returnType = '';
        foreach ($type->getTypes() as $unionType) {
            /** @var \ReflectionUnionType $unionType */
            if (!empty($returnType)) {
                $returnType .= '|';
            }

            $returnType .= $unionType->getName();
        }

        return $returnType;
    }
}