<?php

namespace AgentPlus\Command;

use AgentPlus\AppKernel;
use AgentPlus\Command\Cache\CacheClearCommand;
use AgentPlus\Command\Doctrine\FixturesLoadCommand;
use AgentPlus\Command\Doctrine\MigrationsDiffCommand;
use AgentPlus\Command\Doctrine\MigrationsExecuteCommand;
use AgentPlus\Command\Doctrine\MigrationsMigrateCommand;
use AgentPlus\Command\Doctrine\MigrationsStatusCommand;
use AgentPlus\Command\User\UserChangePasswordCommand;
use AgentPlus\Command\User\UserCreateCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputOption;

class Application extends BaseApplication
{
    /**
     * @var AppKernel
     */
    private $kernel;

    /**
     * Construct
     *
     * @param AppKernel $kernel
     */
    public function __construct(AppKernel $kernel)
    {
        parent::__construct('global_pusher', '1.0');

        $this->kernel = $kernel;

        $this->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', $kernel->getEnvironment()));
        $this->getDefinition()->addOption(new InputOption('--no-debug', null, InputOption::VALUE_NONE, 'Switches off debug mode.'));

        $this->registerCommands();
    }

    /**
     * Register commands
     */
    private function registerCommands()
    {
        $kernel = $this->kernel;

        $this->addCommands([
            // Doctrine Migrations
            new MigrationsDiffCommand($kernel->getDbMigrationsSchemaProvider(), $kernel->getDbMigrationsConfiguration()),
            new MigrationsStatusCommand($kernel->getDbMigrationsConfiguration()),
            new MigrationsExecuteCommand($kernel->getDbMigrationsConfiguration()),
            new MigrationsMigrateCommand($kernel->getDbMigrationsConfiguration()),

            // User commands
            new UserCreateCommand($kernel->getUserPasswordUpdater(), $kernel->getUserRepository(), $kernel->getOrmTransactional()),
            new UserChangePasswordCommand($kernel->getUserPasswordUpdater(), $kernel->getUserRepository(), $kernel->getOrmTransactional()),

            // Cache commands
            new CacheClearCommand($kernel->getCacheClearer(), $kernel->getCacheDir())
        ]);

        if ($kernel->isDebug()) {
            // Fixtures
            $this->addCommands([
                new FixturesLoadCommand($kernel->getDbOrmFixturesExecutor(), $kernel->getDbOrmFixturesLoader())
            ]);
        }
    }
}
