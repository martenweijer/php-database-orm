<?php

namespace Electronics\Database\ORM\Annotations;

use Electronics\Database\ORM\Typings\Fetch;

#[\Attribute]
class OneToOne
{
    public ?string $column;
    public Fetch $fetchType;

    public function __construct(?string $column = null, Fetch $fetchType = Fetch::EAGER)
    {
        $this->column = $column;
        $this->fetchType = $fetchType;
    }
}