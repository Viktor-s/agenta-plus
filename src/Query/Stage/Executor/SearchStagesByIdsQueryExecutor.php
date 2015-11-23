<?php

namespace AgentPlus\Query\Stage\Executor;

use AgentPlus\Entity\Order\Stage;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use AgentPlus\Query\Stage\SearchStagesByIdsQuery;
use FiveLab\Component\Exception\UnexpectedTypeException;

/**
 * Executor for search stages by ids
 *
 * @internal
 */
class SearchStagesByIdsQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchStagesByIdsQuery) {
            throw UnexpectedTypeException::create($query, SearchStagesByIdsQuery::class);
        }

        return $this->em->createQueryBuilder()
            ->from(Stage::class, 's')
            ->select('s')
            ->andWhere('s.id IN (:ids)')
            ->setParameter('ids', $query->getIds())
            ->getQuery()
            ->getResult();
    }
}
