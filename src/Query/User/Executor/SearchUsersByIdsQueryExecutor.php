<?php

namespace AgentPlus\Query\User\Executor;

use AgentPlus\Entity\User\User;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use AgentPlus\Query\User\SearchUsersByIdsQuery;
use FiveLab\Component\Exception\UnexpectedTypeException;

class SearchUsersByIdsQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchUsersByIdsQuery) {
            throw UnexpectedTypeException::create($query, SearchUsersByIdsQuery::class);
        }

        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.id IN (:ids)')
            ->setParameter('ids', $query->getIds())
            ->getQuery()
            ->getResult();
    }
}
