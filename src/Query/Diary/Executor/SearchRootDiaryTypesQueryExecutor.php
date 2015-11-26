<?php

namespace AgentPlus\Query\Diary\Executor;

use AgentPlus\Entity\Diary\Type;
use AgentPlus\Query\Diary\SearchRootDiaryTypesQuery;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use FiveLab\Component\Exception\UnexpectedTypeException;

/**
 * Executor for search root diary types
 *
 * @internal
 */
class SearchRootDiaryTypesQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchRootDiaryTypesQuery) {
            throw UnexpectedTypeException::create($query, SearchRootDiaryTypesQuery::class);
        }

        return $this->em->createQueryBuilder()
            ->from(Type::class, 't')
            ->select('t')
            ->andWhere('t.parent IS NULL')
            ->orderBy('t.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
