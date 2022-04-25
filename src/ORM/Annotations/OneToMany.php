<?php

namespace Electronics\Database\ORM\Annotations;

use Electronics\Database\ORM\Typings\Fetch;

#[\Attribute]
class OneToMany
{
    public string $value;
    public ?string $column;
    public Fetch $fetchType;

    public function __construct(string $value, ?string $column = null, Fetch $fetchType = Fetch::EAGER)
    {
        $this->value = $value;
        $this->column = $column;
        $this->fetchType = $fetchType;
    }
}