<?php

namespace Electronics\Database\DBAL\Constraints;

class SimpleParameterFactory implements ParameterFactory
{
    protected string $prefix;
    protected int $count;

    protected array $parameters = [];

    public function __construct(string $prefix = 'param_', int $count = 0)
    {
        $this->prefix = $prefix;
        $this->count = $count;
    }

    public function generateParameter(float|int|string $value): string
    {
        $key = ':'. $this->prefix . $this->count++;
        $this->parameters[$key] = $value;
        return $key;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}