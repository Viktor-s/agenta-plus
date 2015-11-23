<?php

namespace AgentPlus\Query\Client\Executor;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Query\Client\SearchCitiesQuery;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use FiveLab\Component\Exception\UnexpectedTypeException;

/**
 * Executor for search cities
 *
 * @internal
 */
class SearchCitiesQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchCitiesQuery) {
            throw UnexpectedTypeException::create($query, SearchCitiesQuery::class);
        }

        $result = $this->em->createQueryBuilder()
            ->from(Client::class, 'c')
            ->select('c.city AS city')
            ->andWhere('c.city IS NOT NULL')
            ->groupBy('c.city')
            ->orderBy('c.city', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $cities = [];

        foreach ($result as $item) {
            $cities[] = $item['city'];
        }

        return $cities;
    }
}
