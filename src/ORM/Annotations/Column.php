<?php

namespace Electronics\Database\ORM\Annotations;

use Electronics\Database\ORM\Typings\ColumnType;

#[\Attribute]
class Column
{
    public ?string $value;
    public ColumnType $columnType;

    public function __construct(string $value = null, ColumnType $columnType = ColumnType::STRING)
    {
        $this->value = $value;
        $this->columnType = $columnType;
    }
}