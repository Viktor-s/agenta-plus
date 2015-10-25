<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Repository\Query\FactoryQuery;
use Doctrine\ORM\EntityManagerInterface;
use FiveLab\Component\Pagination\Doctrine\ORM\DefaultPagination;

class FactoryRepository
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
     * Add factory to storage
     *
     * @param Factory $factory
     */
    public function add(Factory $factory)
    {
        $this->em->persist($factory);
    }

    /**
     * Remove factory from storage
     *
     * @param Factory $factory
     */
    public function remove(Factory $factory)
    {
        $this->em->remove($factory);
    }

    /**
     * Find factory by id
     *
     * @param string $id
     *
     * @return Factory|null
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f')
            ->andWhere('f.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find by
     *
     * @param FactoryQuery $query
     * @param int          $page
     * @param int          $limit
     *
     * @return Factory[]
     */
    public function findBy(FactoryQuery $query, $page = null, $limit = 30)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f');

        if (null === $page) {
            return $qb->getQuery()->getResult();
        }

        $pagination = new DefaultPagination();
        $pagination->paginate($qb, $page, $limit);

        return $pagination;
    }
}
