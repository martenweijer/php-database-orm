<?php

namespace Electronics\Database\ORM\Annotations;

use Electronics\Database\ORM\Typings\ColumnType;

#[\Attribute]
class Column
{
    public ?string $value;

    public function __construct(string $value = null)
    {
        $this->value = $value;
    }
}