<?php

namespace Electronics\Database\DBAL;

use Electronics\Database\DBAL\Constraints\Constraint;
use Electronics\Database\DBAL\Types\OrderType;

interface SelectBuilder extends Builder
{
    function addConstraint(Constraint $constraint): static;
    function setLimit(int $limit): static;
    function orderBy(string $column, OrderType $orderType): static;
}