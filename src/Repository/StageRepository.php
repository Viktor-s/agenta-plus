<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Order\Stage;
use Doctrine\ORM\EntityManagerInterface;

class StageRepository
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
     * Add stage to storage
     *
     * @param Stage $stage
     */
    public function add(Stage $stage)
    {
        $this->em->persist($stage);
    }

    /**
     * Remove stage from storage
     *
     * @param Stage $stage
     */
    public function remove(Stage $stage)
    {
        $this->em->remove($stage);
    }

    /**
     * Find stage by id
     *
     * @param string $id
     *
     * @return Stage|null
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(Stage::class, 's')
            ->select('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all stages
     *
     * @return Stage[]
     */
    public function findAll()
    {
        return $this->em->createQueryBuilder()
            ->from(Stage::class, 's')
            ->select('s')
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
