<?php

namespace Electronics\Database\DBAL;

use Electronics\Database\DBAL\Constraints\Constraint;

interface DeleteBuilder extends Builder
{
    function addConstraint(Constraint $constraint): static;
}