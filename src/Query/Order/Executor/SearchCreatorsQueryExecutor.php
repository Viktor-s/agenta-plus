<?php

namespace AgentPlus\Query\Order\Executor;

use AgentPlus\Entity\Order\Order;
use AgentPlus\Entity\User\User;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use AgentPlus\Query\Order\SearchCreatorsQuery;
use FiveLab\Component\Exception\UnexpectedTypeException;

/**
 * Executor for search order creators
 */
class SearchCreatorsQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchCreatorsQuery) {
            throw UnexpectedTypeException::create($query, SearchCreatorsQuery::class);
        }

        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->innerJoin(Order::class, 'o', 'WITH', 'o.creator = u.id')
            ->getQuery()
            ->getResult();
    }
}
