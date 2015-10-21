<?php

namespace AgentPlus\Command\Doctrine;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Provider\SchemaProviderInterface;
use Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand;

class MigrationsDiffCommand extends DiffCommand
{
    /**
     * Construct
     *
     * @param SchemaProviderInterface $schemaProvider
     * @param Configuration           $configuration
     */
    public function __construct(SchemaProviderInterface $schemaProvider = null, Configuration $configuration)
    {
        parent::__construct($schemaProvider);

        $this->setMigrationConfiguration($configuration);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('doctrine:migrations:diff');
    }
}
