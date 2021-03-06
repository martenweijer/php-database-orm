<?php

namespace Electronics\Database\ORM\Mappings;

class EntityMap
{
    protected string $class;
    protected string $table;
    protected ?string $connection;
    protected \ReflectionClass $reflectionClass;

    protected ?PropertyMap $identity = null;
    /** @var OneToOneMap[] */
    protected array $oneToOneMappings = [];
    /** @var OneToManyMap[] */
    protected array $oneToManyMappings = [];
    /** @var PropertyMap[] */
    protected array $properties = [];

    public function __construct(string $class, string $table, ?string $connection, \ReflectionClass $reflectionClass)
    {
        $this->class = $class;
        $this->table = $table;
        $this->connection = $connection;
        $this->reflectionClass = $reflectionClass;
    }

    public function newInstance(): object
    {
        return $this->reflectionClass->newInstance();
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getConnection(): ?string
    {
        return $this->connection;
    }

    public function getIdentity(): PropertyMap
    {
        if ($this->identity === null) {
            throw new \RuntimeException(sprintf('No @Id found on entity "%s".', $this->class));
        }

        return $this->identity;
    }

    public function setIdentity(PropertyMap $identity): void
    {
        $this->identity = $identity;
    }

    /**
     * @return PropertyMap[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function addProperty(PropertyMap $propertyMap): void
    {
        $this->properties[$propertyMap->getName()] = $propertyMap;
    }

    public function getProperty(string $name): PropertyMap
    {
        if (!isset($this->properties[$name])) {
            throw new \InvalidArgumentException(sprintf('Property "%s" not found on class "%s".', $name, $this->class));
        }

        return $this->properties[$name];
    }

    public function addOneToOneMap(OneToOneMap $map): void
    {
        $this->oneToOneMappings[] = $map;
    }

    /**
     * @return OneToOneMap[]
     */
    public function getOneToOneMappings(): array
    {
        return $this->oneToOneMappings;
    }

    public function addOneToManyMap(OneToManyMap $map): void
    {
        $this->oneToManyMappings[] = $map;
    }

    /**
     * @return OneToManyMap[]
     */
    public function getOneToManyMappings(): array
    {
        return $this->oneToManyMappings;
    }
}