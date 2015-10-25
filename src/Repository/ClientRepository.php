<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Repository\Query\ClientQuery;
use Doctrine\ORM\EntityManagerInterface;
use FiveLab\Component\Pagination\Doctrine\ORM\DefaultPagination;

class ClientRepository
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
     * Add client to storage
     *
     * @param Client $client
     */
    public function add(Client $client)
    {
        $this->em->persist($client);
    }

    /**
     * Remove client
     *
     * @param Client $client
     */
    public function remove(Client $client)
    {
        $this->em->remove($client);
    }

    /**
     * Find client by id
     *
     * @param string $id
     *
     * @return Client|null
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(Client::class, 'c')
            ->select('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find by
     *
     * @param ClientQuery $query
     * @param int         $page
     * @param int         $limit
     *
     * @return ClientRepository
     */
    public function findBy(ClientQuery $query, $page = null, $limit = null)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->from(Client::class, 'c')
            ->select('c');

        $qb
            ->orderBy('c.createdAt', 'DESC');

        if (null === $page) {
            return $qb->getQuery()->getResult();
        }

        $pagination = new DefaultPagination();
        $pagination->paginate($qb, $page, $limit);

        return $pagination;
    }
}
