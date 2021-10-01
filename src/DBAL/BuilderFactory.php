<?php

namespace Electronics\Database\DBAL;

interface BuilderFactory
{
    function createSelectBuilder(string $table): SelectBuilder;
    function createInsertBuilder(string $table): InsertBuilder;
    function createUpdateBuilder(string $table): UpdateBuilder;
    function createDeleteBuilder(string $table): DeleteBuilder;
}