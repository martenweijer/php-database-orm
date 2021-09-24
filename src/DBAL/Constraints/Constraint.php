<?php

namespace Electronics\Database\DBAL\Constraints;

interface Constraint
{
    function generateSql(ParameterFactory $factory): string;
}