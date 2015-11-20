<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Entity\User\User;
use AgentPlus\Repository\Query\DiaryQuery;
use Doctrine\ORM\EntityManagerInterface;
use FiveLab\Component\Pagination\Doctrine\ORM\DefaultPagination;

class DiaryRepository
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
     * Add diary
     *
     * @param Diary $diary
     */
    public function add(Diary $diary)
    {
        $this->em->persist($diary);
    }

    /**
     * Remove diary
     *
     * @param Diary $diary
     */
    public function remove(Diary $diary)
    {
        $this->em->remove($diary);
    }

    /**
     * Find diary
     *
     * @param string $id
     *
     * @return Diary|null
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(Diary::class, 'd')
            ->select('d')
            ->andWhere('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find diaries by query
     *
     * @param DiaryQuery $query
     * @param int        $page
     * @param int        $limit
     *
     * @return Diary[]
     */
    public function findBy(DiaryQuery $query, $page = 1, $limit = 50)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Diary::class, 'd')
            ->select('d');

        if ($query->hasFactories()) {
            $factoryIds = array_map(function (Factory $factory) {
                return $factory->getId();
            }, $query->getFactories());

            $qb
                ->innerJoin('d.factories', 'f')
                ->andWhere('f.id IN (:factory_ids)')
                ->setParameter('factory_ids', $factoryIds);
        }

        if ($query->hasClients()) {
            $clientIds = array_map(function (Client $client) {
                return $client->getId();
            }, $query->getClients());

            $qb
                ->innerJoin('d.client', 'cl')
                ->andWhere('cl.id IN (:client_ids)')
                ->setParameter('client_ids', $clientIds);
        }

        if ($query->hasCreators()) {
            $creatorIds = array_map(function (User $user) {
                return $user->getId();
            }, $query->getCreators());

            $qb
                ->innerJoin('d.creator', 'cr')
                ->andWhere('cr.id IN (:creator_ids)')
                ->setParameter('creator_ids', $creatorIds);
        }

        $qb->orderBy('d.createdAt', 'DESC');

        if (null === $page) {
            return $qb->getQuery()->getResult();
        }

        $pagination = new DefaultPagination();
        $pagination->paginate($qb, $page, $limit);

        return $pagination;
    }
}
