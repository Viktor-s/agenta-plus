<?php

namespace AgentPlus\Command\Doctrine;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand;

class MigrationsStatusCommand extends StatusCommand
{
    /**
     * Construct
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct();

        $this->setMigrationConfiguration($configuration);
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('doctrine:migrations:status');
    }
}
