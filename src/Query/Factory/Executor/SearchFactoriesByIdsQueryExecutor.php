<?php

namespace AgentPlus\Query\Factory\Executor;

use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use AgentPlus\Query\Factory\SearchFactoriesByIdsQuery;
use FiveLab\Component\Exception\UnexpectedTypeException;

/**
 * Executor for search factories by ids
 *
 * @internal
 */
class SearchFactoriesByIdsQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchFactoriesByIdsQuery) {
            throw UnexpectedTypeException::create($query, SearchFactoriesByIdsQuery::class);
        }

        return $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f')
            ->andWhere('f.id IN (:ids)')
            ->setParameter('ids', $query->getIds())
            ->getQuery()
            ->getResult();
    }
}
