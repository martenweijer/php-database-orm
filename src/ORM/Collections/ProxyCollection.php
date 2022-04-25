<?php

namespace Electronics\Database\ORM\Collections;

class ProxyCollection extends EntityCollection
{
    private bool $isInitialized = false;
    private \Closure $initializer;

    public function __construct(callable $initializer)
    {
        parent::__construct();
        $this->initializer = $initializer;
    }

    public function getIterator(): \ArrayIterator
    {
        $this->initialize();
        return parent::getIterator();
    }

    public function count(): int
    {
        $this->initialize();
        return parent::count();
    }

    public function offsetExists($offset): bool
    {
        $this->initialize();
        return parent::offsetExists($offset);
    }

    public function offsetGet($offset): mixed
    {
        $this->initialize();
        return parent::offsetGet($offset);
    }

    public function offsetSet($offset, $value): void
    {
        $this->initialize();
        parent::offsetSet($offset, $value);
    }

    public function offsetUnset($offset): void
    {
        $this->initialize();
        parent::offsetUnset($offset);
    }

    private function initialize()
    {
        if (!$this->isInitialized) {
            $this->isInitialized = true;

            call_user_func($this->initializer, $this);
        }
    }
}