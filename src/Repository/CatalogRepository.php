<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Catalog\Catalog;
use AgentPlus\Repository\Query\CatalogQuery;
use Doctrine\ORM\EntityManagerInterface;
use FiveLab\Component\Pagination\Doctrine\ORM\DefaultPagination;

class CatalogRepository
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
     * Add a catalog to storage
     *
     * @param Catalog $catalog
     */
    public function add(Catalog $catalog)
    {
        $this->em->persist($catalog);
    }

    /**
     * Remove catalog
     *
     * @param Catalog $catalog
     */
    public function remove(Catalog $catalog)
    {
        $this->em->remove($catalog);
    }

    /**
     * Find catalog by id
     *
     * @param string $id
     *
     * @return Catalog|null
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(Catalog::class, 'c')
            ->select('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find catalog by query
     *
     * @param CatalogQuery $query
     * @param int          $page
     * @param int          $limit
     *
     * @return Catalog[]
     */
    public function findBy(CatalogQuery $query, $page = null, $limit = 30)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Catalog::class, 'c')
            ->select('c')
            ->orderBy('c.createdAt', 'DESC');

        if ($page === null) {
            return $qb->getQuery()->getResult();
        }

        $paginator = new DefaultPagination();
        $paginator->paginate($qb, $page, $limit);

        return $paginator;
    }
}
