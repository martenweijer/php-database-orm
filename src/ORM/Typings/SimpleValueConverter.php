<?php

namespace Electronics\Database\ORM\Typings;

use Electronics\Database\ORM\Mappings\PropertyMap;

class SimpleValueConverter implements ValueConverter
{
    public function convert(mixed $value, PropertyMap $propertyMap): mixed
    {
        $type = $propertyMap->getColumnType();

        return match ($type) {
            ColumnType::STRING => $value,
            ColumnType::INT => (int) $value,
            ColumnType::FLOAT => (float) $value,
            ColumnType::DATE => new \DateTime($value),
            ColumnType::DATETIME => new \DateTime($value),
        };
    }
}