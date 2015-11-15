<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Order\Order;
use AgentPlus\Repository\Query\OrderQuery;
use Doctrine\ORM\EntityManagerInterface;
use FiveLab\Component\Pagination\Doctrine\ORM\DefaultPagination;

class OrderRepository
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
     * Add order to storage
     *
     * @param Order $order
     */
    public function add(Order $order)
    {
        $this->em->persist($order);
    }

    /**
     * Remove order
     *
     * @param Order $order
     */
    public function remove(Order $order)
    {
        $this->em->remove($order);
    }

    /**
     * Find order by id
     *
     * @param string $id
     *
     * @return Order|null
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(Order::class, 'o')
            ->select('o')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find order by query
     *
     * @param OrderQuery $query
     * @param int        $page
     * @param int        $limit
     *
     * @return Order[]
     */
    public function findBy(OrderQuery $query, $page = null, $limit = null)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Order::class, 'o')
            ->select('o');

        $qb->orderBy('o.createdAt', 'DESC');

        if ($page === null) {
            return $qb->getQuery()->getResult();
        }

        $paginator = new DefaultPagination();
        $paginator->paginate($qb, $page, $limit);

        return $paginator;
    }
}
