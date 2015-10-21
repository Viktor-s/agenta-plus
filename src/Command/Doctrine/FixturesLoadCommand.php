<?php

namespace AgentPlus\Command\Doctrine;

use AgentPlus\Component\Doctrine\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class FixturesLoadCommand extends Command
{
    /**
     * @var Loader
     */
    private $loader;

    /**
     * @var ORMExecutor
     */
    private $executor;

    /**
     * Construct
     *
     * @param ORMExecutor $executor
     * @param Loader      $loader
     */
    public function __construct(ORMExecutor $executor, Loader $loader)
    {
        parent::__construct();

        $this->executor = $executor;
        $this->loader = $loader;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('doctrine:fixtures:load')
            ->setDescription('Loads the fixtures for ORM');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->executor->setLogger(function ($message) use ($output) {
            $output->writeln(sprintf('<info>%s</info>', $message));
        });

        /** @var \Symfony\Component\Console\Helper\QuestionHelper $helper */
        $helper = $this->getHelper('question');

        $question = new ConfirmationQuestion(
            '<question>Careful, database will be purged. Do you want to continue Y/N ?</question>',
            true
        );

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $fixtures = $this->loader->getFixtures();
        $this->executor->execute($fixtures);

        $output->writeln([
            null,
            sprintf('<info>Successfully load <comment>%d</comment> fixtures.</info>', count($fixtures))
        ]);
    }
}