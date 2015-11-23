<?php

namespace AgentPlus\Query\Client\Executor;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Model\Collection;
use AgentPlus\Model\Country;
use AgentPlus\Query\Client\SearchCountriesQuery;
use AgentPlus\Query\Executor\EntityManagerAwareInterface;
use AgentPlus\Query\Executor\EntityManagerAwareTrait;
use AgentPlus\Query\Executor\QueryExecutorInterface;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Symfony\Component\Intl\Intl;

/**
 * Executor for search client countries
 *
 * @internal
 */
class SearchCountriesQueryExecutor implements QueryExecutorInterface, EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function execute($query)
    {
        if (!$query instanceof SearchCountriesQuery) {
            throw UnexpectedTypeException::create($query, SearchCountriesQuery::class);
        }

        $result = $this->em->createQueryBuilder()
            ->from(Client::class, 'c')
            ->select('c.country AS country')
            ->andWhere('c.country IS NOT NULL')
            ->orderBy('c.country', 'ASC')
            ->groupBy('c.country')
            ->getQuery()
            ->getArrayResult();

        $countries = [];
        $regionBundle = Intl::getRegionBundle();

        foreach ($result as $item) {
            $code = strtoupper($item['country']);
            $name = $regionBundle->getCountryName($code);

            $countries[] = new Country($code, $name);
        }

        return new Collection($countries);
    }
}
