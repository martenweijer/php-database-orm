<?php

namespace Electronics\Database\ORM\Typings;

use Electronics\Database\ORM\Mappings\PropertyMap;

interface ValueConverter
{
    function convert(mixed $value, PropertyMap $propertyMap): mixed;
}