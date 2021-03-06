<?php

namespace Electronics\Database\DBAL\Constraints;

interface ParameterFactory
{
    function generateParameter(string|int|float|null $value): string;
    function getParameters(): array;
}