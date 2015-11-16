<?php

namespace AgentPlus\Api\Internal\Catalog;

use AgentPlus\Api\Internal\Catalog\Request\CatalogActionRequest;
use AgentPlus\Api\Internal\Catalog\Request\CatalogCreateRequest;
use AgentPlus\Api\Internal\Catalog\Request\CatalogSearchRequest;
use AgentPlus\Component\Uploader\Uploader;
use AgentPlus\Entity\Catalog\Catalog;
use AgentPlus\Entity\Catalog\Image;
use AgentPlus\Exception\Catalog\CatalogNotFoundException;
use AgentPlus\Repository\Query\CatalogQuery;
use AgentPlus\Repository\Query\FactoryQuery;
use AgentPlus\Repository\RepositoryRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CatalogApi
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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * Construct
     *
     * @param RepositoryRegistry            $repositoryRegistry
     * @param TransactionalInterface        $transactional
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param TokenStorageInterface         $tokenStorage
     * @param Uploader                      $uploader
     */
    public function __construct(
        RepositoryRegistry $repositoryRegistry,
        TransactionalInterface $transactional,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        Uploader $uploader
    )
    {
        $this->repositoryRegistry = $repositoryRegistry;
        $this->transactional = $transactional;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->uploader = $uploader;
    }

    /**
     * View one catalog
     *
     * @Action("catalog")
     *
     * @param CatalogActionRequest $request
     *
     * @return Catalog
     */
    public function catalog(CatalogActionRequest $request)
    {
        $catalog = $this->loadCatalog($request->getCatalogId());

        return $catalog;
    }

    /**
     * View all catalogs
     *
     * @Action("catalog.search")
     *
     * @param CatalogSearchRequest $request
     *
     * @return Catalog[]
     */
    public function catalogs(CatalogSearchRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('CATALOG_LIST')) {
            throw new AccessDeniedException();
        }

        $query = new CatalogQuery();

        $catalogs = $this->repositoryRegistry->getCatalogRepository()
            ->findBy($query, $request->getPage(), $request->getLimit());

        return $catalogs;
    }

    /**
     * Create a new catalog
     *
     * @Action("catalog.create")
     *
     * @param CatalogCreateRequest $request
     *
     * @return Catalog
     */
    public function create(CatalogCreateRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('CATALOG_CREATE')) {
            throw new AccessDeniedException();
        }

        $creator = $this->tokenStorage->getToken()->getUser();

        $catalog = new Catalog($creator, $request->getName());

        if ($request->getFactoryIds()) {
            $factories = $this->loadFactories($request->getFactoryIds());
            $catalog->replaceFactories($factories);
        }

        foreach ($request->getImages() as $requestImage) {
            $imagePath = $this->uploader->getTemporaryFilePath($requestImage->getPath());
            $mimeType = MimeTypeGuesser::getInstance()->guess($imagePath);
            $size = (new \SplFileInfo($imagePath))->getSize();
            $webPath = $this->uploader->moveTemporaryFileToWebPath($requestImage->getPath());
            $name = $requestImage->getName();

            $image = new Image($webPath, $name, $size, $mimeType);
            $catalog->addImage($image);
        }

        $this->transactional->execute(function () use ($catalog) {
            $this->repositoryRegistry->getCatalogRepository()
                ->add($catalog);
        });

        return $catalog;
    }

    /**
     * Load factories
     *
     * @param array $ids
     *
     * @return \AgentPlus\Entity\Factory\Factory[]
     */
    public function loadFactories(array $ids)
    {
        $factoryQuery = new FactoryQuery();
        $factoryQuery->withIds($ids);

        $factories = $this->repositoryRegistry->getFactoryRepository()->findBy($factoryQuery);

        return new ArrayCollection($factories);
    }

    /**
     * Load catalog
     *
     * @param string $id
     *
     * @return Catalog
     *
     * @throws CatalogNotFoundException
     */
    public function loadCatalog($id)
    {
        $catalog = $this->repositoryRegistry->getCatalogRepository()->find($id);

        if (!$catalog) {
            throw CatalogNotFoundException::withId($id);
        }

        return $catalog;
    }
}
