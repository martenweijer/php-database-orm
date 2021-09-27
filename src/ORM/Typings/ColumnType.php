<?php

namespace Electronics\Database\ORM\Typings;

enum ColumnType
{
    case STRING;
    case INT;
    case FLOAT;
    case DATE;
    case DATETIME;
}