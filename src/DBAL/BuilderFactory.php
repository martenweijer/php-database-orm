<?php

namespace Electronics\Database\DBAL;

interface BuilderFactory
{
    function createSelectBuilder(string $table): SelectBuilder;
}