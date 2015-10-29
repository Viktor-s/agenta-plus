<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;

class CurrencyRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Add currency to storage
     *
     * @param Currency $currency
     */
    public function add(Currency $currency)
    {
        $this->em->persist($currency);
    }

    /**
     * Remove currency from storage
     *
     * @param Currency $currency
     */
    public function remove(Currency $currency)
    {
        $this->em->remove($currency);
    }

    /**
     * Find currency by code
     *
     * @param string $code
     *
     * @return Currency|null
     */
    public function find($code)
    {
        return $this->em->createQueryBuilder()
            ->from(Currency::class, 'c')
            ->select('c')
            ->andWhere('c.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find currencies
     *
     * @return Currency[]
     */
    public function findAll()
    {
        return $this->em->createQueryBuilder()
            ->from(Currency::class, 'c')
            ->select('c')
            ->getQuery()
            ->getResult();
    }
}
