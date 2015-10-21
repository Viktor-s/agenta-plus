<?php

namespace AgentPlus\Command\Doctrine;

use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand;

class MigrationsExecuteCommand extends ExecuteCommand
{
    /**
     * {@inheritDoc}
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

        $this->setName('doctrine:migrations:execute');
    }
}
