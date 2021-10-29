<?php

namespace Electronics\Database\DBAL;

use Electronics\Database\DBAL\Constraints\Constraint;

interface UpdateBuilder extends Builder
{
    function set(string $column, string|int|float|null $value): static;
    function addConstraint(Constraint $constraint): static;
}