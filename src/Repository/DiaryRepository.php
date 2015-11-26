<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\Diary\Type;
use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Entity\Order\Stage;
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
            ->select('d')
            ->leftJoin('d.client', 'cl')
            ->distinct();

        if ($query->hasTypes()) {
            $typeIds = array_map(function (Type $type) {
                return $type->getId();
            }, $query->getTypes());

            $qb
                ->innerJoin('d.type', 'dt')
                ->andWhere('dt.id IN (:diary_type_ids)')
                ->setParameter('diary_type_ids', $typeIds);
        }

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

        if ($query->hasStages()) {
            $stageIds = array_map(function (Stage $stage) {
                return $stage->getId();
            }, $query->getStages());

            $qb
                ->innerJoin('d.stage', 's')
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
                    ->andWhere('d.createdAt >= :created_at_from')
                    ->setParameter('created_at_from', $created->getFrom());
            }

            if ($created->getTo()) {
                $qb
                    ->andWhere('d.createdAt <= :created_at_to')
                    ->setParameter('created_at_to', $created->getTo());
            }
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
