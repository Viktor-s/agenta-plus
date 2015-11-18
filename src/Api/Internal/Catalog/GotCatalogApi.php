<?php

namespace AgentPlus\Api\Internal\Catalog;

use AgentPlus\Api\Internal\Catalog\Request\GotCatalogSearchRequest;
use AgentPlus\Repository\Query\GotCatalogQuery;
use AgentPlus\Repository\RepositoryRegistry;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GotCatalogApi
{
    /**
     * @var RepositoryRegistry
     */
    private $repositoryRegistry;

    /**
     * @var TransactionalInterface
     */
    private $transactional;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Construct
     *
     * @param RepositoryRegistry            $registry
     * @param TransactionalInterface        $transactional
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        RepositoryRegistry $registry,
        TransactionalInterface $transactional,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->repositoryRegistry = $registry;
        $this->transactional = $transactional;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * View all got catalogs
     *
     * @Action("got_catalog.search")
     *
     * @param GotCatalogSearchRequest $request
     *
     * @return \AgentPlus\Entity\Catalog\GotCatalog[]
     */
    public function gotCatalogs(GotCatalogSearchRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('GOT_CATALOG_LIST')) {
            throw new AccessDeniedException();
        }

        $query = new GotCatalogQuery();
        $gotCatalogs = $this->repositoryRegistry->getGotCatalogRepository()
            ->findBy($query, $request->getPage(), $request->getLimit());

        return $gotCatalogs;
    }
}
