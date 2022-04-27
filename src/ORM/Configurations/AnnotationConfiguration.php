<?php

namespace Electronics\Database\ORM\Configurations;

use Electronics\Database\ORM\Annotations\Column;
use Electronics\Database\ORM\Annotations\Entity;
use Electronics\Database\ORM\Annotations\Id;
use Electronics\Database\ORM\Annotations\OneToMany;
use Electronics\Database\ORM\Annotations\OneToOne;
use Electronics\Database\ORM\Mappings\EntityMap;
use Electronics\Database\ORM\Mappings\OneToManyMap;
use Electronics\Database\ORM\Mappings\OneToOneMap;
use Electronics\Database\ORM\Mappings\PropertyMap;
use Electronics\Database\ORM\Typings\ColumnType;

class AnnotationConfiguration implements Configuration
{
    /** @var EntityMap[] */
    protected array $entityMaps = [];

    public function retrieveEntityMap(string|object $entity): EntityMap
    {
        $className = $this->convertToClassName($entity);

        if (!isset($this->entityMaps[$className])) {
            $this->entityMaps[$className] = $this->generateEntityMap($className);
        }

        return $this->entityMaps[$className];
    }

    protected function generateEntityMap(string $className): EntityMap
    {
        /** @var class-string $className */
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

        return $entityMap;
    }

    protected function addProperty(EntityMap $entityMap, \ReflectionProperty $reflectionProperty): void
    {
        $attributes = $reflectionProperty->getAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute->getName() === Column::class) {
                $reflectionProperty->setAccessible(true);

                /** @var Column $annotation */
                $annotation = $attribute->newInstance();
                $entityMap->addProperty(new PropertyMap($reflectionProperty->getName(),
                    $annotation->value ?? $reflectionProperty->getName(),
                    $this->convertPropertyToColumnType($reflectionProperty), $reflectionProperty));
            }

            elseif ($attribute->getName() === Id::class) {
                $reflectionProperty->setAccessible(true);

                /** @var Id $annotation */
                $annotation = $attribute->newInstance();
                $entityMap->setIdentity(new PropertyMap($reflectionProperty->getName(),
					$annotation->value ?? $reflectionProperty->getName(),
                    $this->convertPropertyToColumnType($reflectionProperty), $reflectionProperty));
            }

            elseif ($attribute->getName() === OneToOne::class) {
                $reflectionProperty->setAccessible(true);

                /** @var OneToOne $annotation */
                $annotation = $attribute->newInstance();
                /** @var \ReflectionNamedType $type */
                $type = $reflectionProperty->getType();
                $entityMap->addOneToOneMap(new OneToOneMap($reflectionProperty->getName(),
                    $type->getName(), $annotation->column ?? $reflectionProperty->getName() .'_id',
                    $annotation->fetchType, $reflectionProperty));
            }

            elseif ($attribute->getName() === OneToMany::class) {
                $reflectionProperty->setAccessible(true);

                /** @var OneToMany $annotation */
                $annotation = $attribute->newInstance();
                $entityMap->addOneToManyMap(new OneToManyMap($reflectionProperty->getName(),
                    $annotation->value, $annotation->column ?? $entityMap->getTable() .'_id',
                    $annotation->fetchType, $reflectionProperty));
            }
        }
    }

    protected function convertToClassName(string|object $entity): string
    {
        if (is_string($entity)) {
            return $entity;
        }

        return get_class($entity);
    }

    protected function convertPropertyToColumnType(\ReflectionProperty $property): ColumnType
    {
        if ($property->hasType()) {
            /** @var \ReflectionNamedType $type */
            $type = $property->getType();
            return match ($type->getName()) {
                'int' => ColumnType::INT,
                'float' => ColumnType::FLOAT,
                'bool' => ColumnType::BOOL,
                'DateTime' => ColumnType::DATETIME,
                default => ColumnType::STRING
            };
        }

        return ColumnType::STRING;
    }
}