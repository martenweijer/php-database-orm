<?php

namespace Electronics\Database\ORM\Annotations;

#[\Attribute]
class OneToOne
{
    public ?string $column;

    public function __construct(?string $column = null)
    {
        $this->column = $column;
    }
}