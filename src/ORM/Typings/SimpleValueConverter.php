<?php

namespace Electronics\Database\ORM\Typings;

use Electronics\Database\ORM\Mappings\PropertyMap;

class SimpleValueConverter implements ValueConverter
{
    public function convertFromSqlValue(mixed $value, PropertyMap $propertyMap): mixed
    {
        $type = $propertyMap->getColumnType();

        return match ($type) {
            ColumnType::STRING => $value,
            ColumnType::INT => (int) $value,
            ColumnType::FLOAT => (float) $value,
            ColumnType::BOOL => $value == true || $value == 1 || $value == '1',
            ColumnType::DATETIME => $value ? new \DateTime($value) : null,
            default => throw new \InvalidArgumentException(sprintf('Unknown Column Type "%s" for property "%s".', $type->name, $propertyMap->getName()))
        };
    }

    public function convertToSqlValue(mixed $value, PropertyMap $propertyMap): int|string|null
    {
        if (is_null($value)) {
            return null;
        }

        if ($propertyMap->getColumnType() === ColumnType::DATETIME) {
            if (!($value instanceof \DateTime)) {
                throw new \InvalidArgumentException(sprintf('Value "%s" is not of type DateTime.', $value));
            }

            return $value->format('Y-m-d H:i:s');
        }

        if ($propertyMap->getColumnType() === ColumnType::BOOL) {
            return $value ? 1 : 0;
        }

        return $value;
    }
}