<?php

namespace Electronics\Database\DBAL\Constraints;

use PHPUnit\Framework\TestCase;

class SimpleParameterFactoryTest extends TestCase
{
    function testGenerateParameter(): void
    {
        $factory = new SimpleParameterFactory('foo_', 5);
        $this->assertEquals(':foo_5', $factory->generateParameter('5'));
        $this->assertEquals(':foo_6', $factory->generateParameter('6'));
        $this->assertEquals([':foo_5' => '5', ':foo_6' => '6'], $factory->getParameters());
    }
}
