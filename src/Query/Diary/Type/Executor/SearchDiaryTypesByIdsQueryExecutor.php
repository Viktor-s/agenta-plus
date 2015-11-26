<?php

namespace AgentPlus\Query\Diary\Type\Executor;

use AgentPlus\Entity\Diary\Type;
use AgentPlus\Query\Diary\Type\SearchDiaryTypesByIdsQuery;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use FiveLab\Component\Exception\UnexpectedTypeException;

/**
 * Executor for search diary types by ids
 *
 * @internal
 */
class SearchDiaryTypesByIdsQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchDiaryTypesByIdsQuery) {
            throw UnexpectedTypeException::create($query, SearchDiaryTypesByIdsQuery::class);
        }

        return $this->em->createQueryBuilder()
            ->from(Type::class, 't')
            ->select('t')
            ->andWhere('t.id IN (:ids)')
            ->setParameter('ids', $query->getIds())
            ->getQuery()
            ->getResult();
    }
}
