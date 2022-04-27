<?php

namespace Electronics\Database\ORM\Annotations;

#[\Attribute]
class Entity
{
    public string $value;
    public ?string $connection;

    public function __construct(string $value, string $connection = null)
    {
        $this->value = $value;
        $this->connection = $connection;
    }
}