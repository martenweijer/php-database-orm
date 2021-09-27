<?php

namespace Electronics\Database\ORM\Typings;

use Electronics\Database\ORM\Mappings\PropertyMap;
use PHPUnit\Framework\TestCase;

class SimpleValueConverterTest extends TestCase
{
    function testConvert(): void
    {
        $converter = new SimpleValueConverter();

        $property = new \ReflectionProperty(SimpleValueConverterTest::class, 'backupGlobals');

        $this->assertEquals(1, $converter->convert('1', new PropertyMap('', '', ColumnType::INT, $property)));
        $this->assertEquals('foo', $converter->convert('foo', new PropertyMap('', '', ColumnType::STRING, $property)));
        $this->assertEquals(1.5, $converter->convert('1.5', new PropertyMap('', '', ColumnType::FLOAT, $property)));
        $this->assertEquals(new \DateTime('2021-01-01'), $converter->convert('2021-01-01', new PropertyMap('', '', ColumnType::DATE, $property)));
        $this->assertEquals(new \DateTime('2021-01-01 08:00:00'), $converter->convert('2021-01-01 08:00:00', new PropertyMap('', '', ColumnType::DATETIME, $property)));
    }
}
