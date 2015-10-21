<?php

namespace AgentPlus\Command\Cache;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

class CacheClearCommand extends Command
{
    /**
     * @var CacheClearerInterface
     */
    private $clearer;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * Construct
     *
     * @param CacheClearerInterface $cacheClearer
     * @param string                $cacheDir
     */
    public function __construct(CacheClearerInterface $cacheClearer, $cacheDir)
    {
        parent::__construct();

        $this->clearer = $cacheClearer;
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cache:clear')
            ->setDescription('Clears cache');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->clearer->clear($this->cacheDir);

        $output->writeln('<info>Successfully clear caches.</info>');
    }
}
