<?php

namespace AgentPlus\Query\Client\Executor;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Query\Client\SearchClientsByIdsQuery;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use FiveLab\Component\Exception\UnexpectedTypeException;

/**
 * Executor for search clients by ids
 *
 * @internal
 */
class SearchClientsByIdsQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchClientsByIdsQuery) {
            throw UnexpectedTypeException::create($query, SearchClientsByIdsQuery::class);
        }

        return $this->em->createQueryBuilder()
            ->from(Client::class, 'c')
            ->select('c')
            ->andWhere('c.id IN (:ids)')
            ->setParameter('ids', $query->getIds())
            ->getQuery()
            ->getResult();
    }
}
