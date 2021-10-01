<?php

namespace Electronics\Database\DBAL;

interface InsertBuilder extends Builder
{
    function add(string $column, string|int|float|null $value): static;
}