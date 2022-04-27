<?php

namespace Electronics\Database\ORM\Collections;

class EntityCollection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    protected array $entities;

    public function __construct(array $entities = [])
    {
        $this->entities = $entities;
    }

    public function setEntities(array $entities): void
    {
        $this->entities = $entities;
    }

    public function all(): array
    {
        return $this->entities;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->entities);
    }

    public function count(): int
    {
        return count($this->entities);
    }

    /**
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->entities[$offset]);
    }

    /**
     * @psalm-suppress MixedArrayOffset
     * @psalm-suppress MixedReturnStatement
     */
    public function offsetGet($offset): mixed
    {
        return $this->entities[$offset];
    }

    /**
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetSet($offset, $value): void
    {
        $this->entities[$offset] = $value;
    }

    /**
     * @psalm-suppress MixedArrayOffset
     */
    public function offsetUnset($offset): void
    {
        unset($this->entities[$offset]);
    }
}