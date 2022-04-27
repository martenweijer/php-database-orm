<?php

namespace Electronics\Database\ORM\Typings;

use Electronics\Database\ORM\Mappings\PropertyMap;

interface ValueConverter
{
    function convertFromSqlValue(string|float|int|bool|null $value, PropertyMap $propertyMap): mixed;
    function convertToSqlValue(mixed $value, PropertyMap $propertyMap): int|string|null;
}