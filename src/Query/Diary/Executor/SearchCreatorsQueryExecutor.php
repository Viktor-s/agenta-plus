<?php

namespace AgentPlus\Query\Diary\Executor;

use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\User\User;
use AgentPlus\Query\Diary\SearchCreatorsQuery;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use FiveLab\Component\Exception\UnexpectedTypeException;

/**
 * Executor for search diary creators
 *
 * @internal
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
            ->innerJoin(Diary::class, 'd', 'WITH', 'd.creator = u.id')
            ->getQuery()
            ->getResult();
    }
}
