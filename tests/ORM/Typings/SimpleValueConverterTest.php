<?php

namespace Electronics\Database\ORM\Typings;

use Electronics\Database\ORM\Mappings\PropertyMap;
use PHPUnit\Framework\TestCase;

class SimpleValueConverterTest extends TestCase
{
    function testConvertFromSqlValue(): void
    {
        $converter = new SimpleValueConverter();

        $property = new \ReflectionProperty(SimpleValueConverterTest::class, 'backupGlobals');

        $this->assertEquals(1, $converter->convertFromSqlValue('1', new PropertyMap('', '', ColumnType::INT, $property)));
        $this->assertEquals('foo', $converter->convertFromSqlValue('foo', new PropertyMap('', '', ColumnType::STRING, $property)));
        $this->assertEquals(1.5, $converter->convertFromSqlValue('1.5', new PropertyMap('', '', ColumnType::FLOAT, $property)));
        $this->assertEquals(new \DateTime('2021-01-01'), $converter->convertFromSqlValue('2021-01-01', new PropertyMap('', '', ColumnType::DATETIME, $property)));
        $this->assertEquals(new \DateTime('2021-01-01 08:00:00'), $converter->convertFromSqlValue('2021-01-01 08:00:00', new PropertyMap('', '', ColumnType::DATETIME, $property)));
    }

    function testConvertToSqlValue(): void
    {
        $converter = new SimpleValueConverter();

        $property = new \ReflectionProperty(SimpleValueConverterTest::class, 'backupGlobals');

        $this->assertEquals(1, $converter->convertToSqlValue('1', new PropertyMap('', '', ColumnType::INT, $property)));
        $this->assertEquals('foo', $converter->convertToSqlValue('foo', new PropertyMap('', '', ColumnType::STRING, $property)));
        $this->assertEquals(1.5, $converter->convertToSqlValue('1.5', new PropertyMap('', '', ColumnType::FLOAT, $property)));
        $this->assertEquals('2021-01-01 00:00:00', $converter->convertToSqlValue(new \DateTime('2021-01-01'), new PropertyMap('', '', ColumnType::DATETIME, $property)));
        $this->assertEquals('2021-01-01 08:00:00', $converter->convertToSqlValue(new \DateTime('2021-01-01 08:00:00'), new PropertyMap('', '', ColumnType::DATETIME, $property)));
    }
}
