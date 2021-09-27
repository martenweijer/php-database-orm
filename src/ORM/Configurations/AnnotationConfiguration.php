<?php

namespace Electronics\Database\ORM\Configurations;

use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Mappings\PropertyMap;

class AnnotationConfiguration implements Configuration
{
    protected array $entityMaps = [];

    public function retrieveEntityMap(string|\stdClass $entity): EntityMap
    {
        $className = $this->convertToClassName($entity);

        if (!isset($this->entityMaps[$className])) {
            $this->entityMaps[$className] = $this->generateEntityMap($className);
        }

        return $this->entityMaps[$className];
    }

    protected function generateEntityMap(string $className): EntityMap
    {
        $reflection = new \ReflectionClass($className);
        $attributes = $reflection->getAttributes(Entity::class);

        if (count($attributes) !== 1) {
            throw new \RuntimeException(sprintf('No @Entity annotation found on entity "%s".', $className));
        }

        $annotation = $attributes[0]->newInstance();
        $entityMap = new EntityMap($className, $annotation->value, $annotation->connection, $reflection);

        foreach ($reflection->getProperties() as $property) {
            $this->addProperty($entityMap, $property);
        }

        if ($entityMap->getIdentity() === null) {
            throw new \RuntimeException(sprintf('No @Id found on entity "%s".', $entityMap->getClass()));
        }

        return $entityMap;
    }

    protected function addProperty(EntityMap $entityMap, \ReflectionProperty $reflectionProperty): void
    {
        $attributes = $reflectionProperty->getAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute->getName() === Column::class) {
                $reflectionProperty->setAccessible(true);

                $annotation = $attribute->newInstance();
                $entityMap->addProperty(new PropertyMap($reflectionProperty->getName(),
                    $annotation->value ?? $reflectionProperty->getName(),
                    $annotation->columnType, $reflectionProperty));
            }

            elseif ($attribute->getName() === Id::class) {
                $reflectionProperty->setAccessible(true);

                $annotation = $attribute->newInstance();
                $entityMap->setIdentity(new PropertyMap($reflectionProperty->getName(),
					$annotation->value ?? $reflectionProperty->getName(),
                    $annotation->columnType, $reflectionProperty));
            }
        }
    }

    protected function convertToClassName(string|\stdClass $entity): string
    {
        if (is_string($entity)) {
            return $entity;
        }

        if (is_object($entity)) {
            return get_class($entity);

        }

        throw new \InvalidArgumentException(sprintf('Could not convert variable of type "%s" to string', gettype($entity)));
    }
}