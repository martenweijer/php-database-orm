<?php

namespace Electronics\Database\DBAL\Types;

enum OrderType
{
    case ASC;
    case DESC;

    public static function orderTypeFromString(string $direction): OrderType
    {
        if (strtolower($direction) === 'asc') {
            return OrderType::ASC;
        }

        if (strtolower($direction) === 'desc') {
            return OrderType::DESC;
        }

        throw new \InvalidArgumentException(sprintf('Unable to create OrderType from string "%s".', $direction));
    }
}