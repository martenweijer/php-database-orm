<?php

namespace Electronics\Database\ORM;

use Electronics\Database\Connections\Connection;
use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\ORM\Configurations\AnnotationConfiguration;
use Electronics\Database\ORM\Configurations\Configuration;
use Electronics\Database\ORM\Hydrators\EntityHydrator;
use Electronics\Database\ORM\Hydrators\Hydrator;
use Electronics\Database\ORM\Typings\SimpleValueConverter;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;

class DatabaseContext
{
    protected Connection $connection;
    protected Configuration $configuration;
    protected UnitOfWork $unitOfWork;
    protected Hydrator $hydrator;

    public function __construct(Connection $connection, Configuration $configuration = null, UnitOfWork $unitOfWork = null, Hydrator $hydrator = null)
    {
        $this->connection = $connection;
        $this->configuration = $configuration ?: new AnnotationConfiguration();
        $this->unitOfWork = $unitOfWork ?: new UnitOfWork();
        $this->hydrator = $hydrator ?: new EntityHydrator(new SimpleValueConverter(), $this->unitOfWork);
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function getBuilderFactory(): BuilderFactory
    {
        return $this->connection->getBuilderFactory();
    }

    public function getUnitOfWork(): UnitOfWork
    {
        return $this->unitOfWork;
    }

    public function getHydrator(): Hydrator
    {
        return $this->hydrator;
    }
}