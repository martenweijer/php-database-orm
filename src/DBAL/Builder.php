<?php

namespace Electronics\Database\DBAL;

interface Builder
{
    function generateSql(): string;
    function getParameters(): array;
}