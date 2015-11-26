<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Entity\Order\Order;
use AgentPlus\Entity\Order\Stage;
use AgentPlus\Entity\User\User;
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
            ->select('o')
            ->leftJoin('o.client', 'cl')
            ->distinct();

        if ($query->hasFactories()) {
            $factoryIds = array_map(function (Factory $factory) {
                return $factory->getId();
            }, $query->getFactories());

            $qb
                ->innerJoin('o.factory', 'f')
                ->andWhere('f.id IN (:factory_ids)')
                ->setParameter('factory_ids', $factoryIds);
        }

        if ($query->hasClients()) {
            $clientIds = array_map(function (Client $client) {
                return $client->getId();
            }, $query->getClients());

            $qb
                ->andWhere('cl.id IN (:client_ids)')
                ->setParameter('client_ids', $clientIds);
        }

        if ($query->hasCreators()) {
            $creatorIds = array_map(function (User $user) {
                return $user->getId();
            }, $query->getCreators());

            $qb
                ->innerJoin('o.creator', 'cr')
                ->andWhere('cr.id IN (:creator_ids)')
                ->setParameter('creator_ids', $creatorIds);
        }

        if ($query->hasStages()) {
            $stageIds = array_map(function (Stage $stage) {
                return $stage->getId();
            }, $query->getStages());

            $qb
                ->innerJoin('o.stage', 's')
                ->andWhere('s.id IN (:stage_ids)')
                ->setParameter('stage_ids', $stageIds);
        }

        if ($query->hasCountries()) {
            $qb
                ->andWhere('cl.country IN (:countries)')
                ->setParameter('countries', $query->getCountries());
        }

        if ($query->hasCities()) {
            $qb
                ->andWhere('LOWER(cl.city) IN (:cities)')
                ->setParameter('cities', $query->getCities());
        }

        if ($query->hasCreated()) {
            $created = $query->getCreated();

            if ($created->getFrom()) {
                $qb
                    ->andWhere('o.createdAt >= :created_at_from')
                    ->setParameter('created_at_from', $created->getFrom());
            }

            if ($created->getTo()) {
                $qb
                    ->andWhere('o.createdAt <= :created_at_to')
                    ->setParameter('created_at_to', $created->getTo());
            }
        }

        $qb->orderBy('o.createdAt', 'DESC');

        if ($page === null) {
            return $qb->getQuery()->getResult();
        }

        $paginator = new DefaultPagination();
        $paginator->paginate($qb, $page, $limit);

        return $paginator;
    }
}
