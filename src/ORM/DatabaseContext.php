<?php

namespace Electronics\Database\ORM;

use Electronics\Database\Connections\Connection;
use Electronics\Database\DBAL\BuilderFactory;
use Electronics\Database\ORM\Configurations\AnnotationConfiguration;
use Electronics\Database\ORM\Configurations\Configuration;
use Electronics\Database\ORM\Hydrators\EntityHydrator;
use Electronics\Database\ORM\Hydrators\Hydrator;
use Electronics\Database\ORM\Persisting\EntityPersister;
use Electronics\Database\ORM\Persisting\Persister;
use Electronics\Database\ORM\Typings\SimpleValueConverter;
use Electronics\Database\ORM\Typings\ValueConverter;
use Electronics\Database\ORM\UnitOfWork\UnitOfWork;

class DatabaseContext
{
    protected Connection $connection;
    protected Configuration $configuration;
    protected UnitOfWork $unitOfWork;
    protected Hydrator $hydrator;
    protected Persister $persister;

    public function __construct(Connection $connection,
                                Configuration $configuration = null,
                                UnitOfWork $unitOfWork = null,
                                Hydrator $hydrator = null,
                                Persister $persister = null,
                                ValueConverter $valueConverter = null)
    {
        $valueConverter = $valueConverter ?: new SimpleValueConverter();

        $this->connection = $connection;
        $this->configuration = $configuration ?: new AnnotationConfiguration();
        $this->unitOfWork = $unitOfWork ?: new UnitOfWork();
        $this->hydrator = $hydrator ?: new EntityHydrator($valueConverter, $this->unitOfWork);
        $this->persister = $persister ?: new EntityPersister($this->connection, $this->configuration, $valueConverter, $this->unitOfWork);
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

    public function getPersister(): Persister
    {
        return $this->persister;
    }
}