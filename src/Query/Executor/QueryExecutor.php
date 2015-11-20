<?php

namespace AgentPlus\Query\Executor;

use AgentPlus\AppKernel;
use AgentPlus\Component\Kernel\KernelAwareInterface;
use FiveLab\Component\Exception\UnexpectedTypeException;
use AgentPlus\Query\Executor\Exception\InvalidQueryExecutorException;
use AgentPlus\Query\Executor\Exception\QueryExecutorClassNotFoundException;

/**
 * Query executor
 */
class QueryExecutor
{
    /**
     * @var AppKernel
     */
    private $kernel;

    /**
     * @var array|QueryExecutorInterface[]
     */
    private $queryExecutors = [];

    /**
     * Construct
     *
     * @param AppKernel $kernel
     */
    public function __construct(AppKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Execute query
     *
     * @param object $query
     *
     * @return mixed
     */
    public function execute($query)
    {
        if (!is_object($query)) {
            throw UnexpectedTypeException::create($query, 'object');
        }

        $executor = $this->createQueryExecutor($query);

        return $executor->execute($query);
    }

    /**
     * Create query executor
     *
     * @param object $query
     *
     * @return QueryExecutorInterface
     *
     * @throws QueryExecutorClassNotFoundException
     * @throws InvalidQueryExecutorException
     * @throws \Exception
     */
    private function createQueryExecutor($query)
    {
        $queryClass = get_class($query);

        if (isset($this->queryExecutors[$queryClass])) {
            return $this->queryExecutors[$queryClass];
        }

        $classes = [];
        $executor = null;

        $queryExecutorClass = $queryClass . 'Executor';
        $classes[] = $queryExecutorClass;

        if (class_exists($queryExecutorClass)) {
            $executor = new $queryExecutorClass();
        } else {
            $parts = explode('\\', $queryClass);
            $shortClassName = array_pop($parts);
            $queryExecutorClass = implode('\\', $parts) . '\Executor\\' . $shortClassName . 'Executor';
            $classes[] = $queryExecutorClass;

            if (class_exists($queryExecutorClass)) {
                $executor = new $queryExecutorClass;
            }
        }

        if (!$executor) {
            throw QueryExecutorClassNotFoundException::withClasses($queryClass, $classes);
        }


        if (!$executor instanceof QueryExecutorInterface) {
            throw InvalidQueryExecutorException::unexpected($executor, QueryExecutorInterface::class);
        }

        if ($executor instanceof KernelAwareInterface) {
            $executor->setKernel($this->kernel);
        }

        if ($executor instanceof EntityManagerAwareInterface) {
            $executor->setEntityManager($this->kernel['db.orm.em']);
        }

        $this->queryExecutors[$queryClass] = $executor;

        return $executor;
    }
}
