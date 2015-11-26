<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Diary\Type;
use Doctrine\ORM\EntityManagerInterface;

class DiaryTypeRepository
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
     * Add type
     *
     * @param Type $type
     */
    public function add(Type $type)
    {
        $this->em->persist($type);
    }

    /**
     * Remove type
     *
     * @param Type $type
     */
    public function remove(Type $type)
    {
        $this->em->remove($type);
    }

    /**
     * Find type by id
     *
     * @param string $id
     *
     * @return Type|null
     */
    public function find($id)
    {
        return $this->em->find(Type::class, $id);
    }
}
