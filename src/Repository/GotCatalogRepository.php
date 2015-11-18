<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\Catalog\GotCatalog;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Repository\Query\GotCatalogQuery;
use Doctrine\ORM\EntityManagerInterface;
use FiveLab\Component\Pagination\Doctrine\ORM\DefaultPagination;

class GotCatalogRepository
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
     * Add got catalog
     *
     * @param GotCatalog $gotCatalog
     */
    public function add(GotCatalog $gotCatalog)
    {
        $this->em->persist($gotCatalog);
    }

    /**
     * Remove got catalog
     *
     * @param GotCatalog $gotCatalog
     */
    public function remove(GotCatalog $gotCatalog)
    {
        $this->em->remove($gotCatalog);
    }

    /**
     * Find by
     *
     * @param GotCatalogQuery $query
     * @param int             $page
     * @param int             $limit
     *
     * @return GotCatalog[]
     */
    public function findBy(GotCatalogQuery $query, $page = null, $limit = 30)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(GotCatalog::class, 'gc')
            ->select('gc')
            ->orderBy('gc.createdAt', 'DESC');

        if ($page === null) {
            return $qb->getQuery()->getResult();
        }

        $pagination = new DefaultPagination();
        $pagination->paginate($qb, $page, $limit);

        return $pagination;
    }

    /**
     * Find got catalogs by diary
     *
     * @param Diary $diary
     *
     * @return GotCatalog[]
     */
    public function findByDiary(Diary $diary)
    {
        return $this->em->createQueryBuilder()
            ->from(GotCatalog::class, 'gc')
            ->select('gc')
            ->andWhere('gc.diary = :diary')
            ->setParameter('diary', $diary)
            ->getQuery()
            ->getResult();
    }
}
