<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\User\Team;
use AgentPlus\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;

class TeamRepository
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
     * Add team to storage
     *
     * @param Team $team
     */
    public function add(Team $team)
    {
        $this->em->persist($team);
    }

    /**
     * Remove team from storage
     *
     * @param Team $team
     */
    public function remove(Team $team)
    {
        $this->em->remove($team);
    }

    /**
     * Find by key
     *
     * @param string $id
     *
     * @return Team|null
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(Team::class, 't')
            ->select('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find by owner
     *
     * @param User $owner
     *
     * @return Team[]
     */
    public function findByOwner(User $owner)
    {
        return $this->em->createQueryBuilder()
            ->from(Team::class, 't')
            ->select('t')
            ->andWhere('t.owner = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getResult();
    }
}
