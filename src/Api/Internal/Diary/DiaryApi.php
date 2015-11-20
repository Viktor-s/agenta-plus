<?php

namespace AgentPlus\Api\Internal\Diary;

use AgentPlus\Api\Internal\Diary\Request\DiaryActionRequest;
use AgentPlus\Api\Internal\Diary\Request\DiaryCreateRequest;
use AgentPlus\Api\Internal\Diary\Request\DiarySearchRequest;
use AgentPlus\Api\Internal\Diary\Request\DiaryUpdateRequest;
use AgentPlus\Api\Internal\Diary\Request\Money as MoneyRequest;
use AgentPlus\Component\Uploader\Uploader;
use AgentPlus\Entity\Catalog\GotCatalog;
use AgentPlus\Entity\Diary\Attachment;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\Diary\Money;
use AgentPlus\Exception\Catalog\CatalogNotFoundException;
use AgentPlus\Exception\Client\ClientNotFoundException;
use AgentPlus\Exception\Currency\CurrencyNotFoundException;
use AgentPlus\Exception\Diary\DiaryNotFoundException;
use AgentPlus\Exception\Factory\FactoryNotFoundException;
use AgentPlus\Query\Client\SearchClientsByIdsQuery;
use AgentPlus\Query\Diary\SearchCreatorsQuery;
use AgentPlus\Query\Executor\QueryExecutor;
use AgentPlus\Query\Factory\SearchFactoriesByIdsQuery;
use AgentPlus\Query\User\SearchUsersByIdsQuery;
use AgentPlus\Repository\Query\DiaryQuery;
use AgentPlus\Repository\RepositoryRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DiaryApi
{
    /**
     * @var QueryExecutor
     */
    private $queryExecutor;

    /**
     * @var RepositoryRegistry
     */
    private $repositoryRegistry;

    /**
     * @var TransactionalInterface
     */
    private $transactional;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Uploader
     */
    private $uploader;

    /**
     * Construct
     *
     * @param QueryExecutor                 $queryExecutor
     * @param RepositoryRegistry            $repositoryRegistry
     * @param TransactionalInterface        $transactional
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Uploader                      $uploader
     */
    public function __construct(
        QueryExecutor $queryExecutor,
        RepositoryRegistry $repositoryRegistry,
        TransactionalInterface $transactional,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        Uploader $uploader
    )
    {
        $this->queryExecutor = $queryExecutor;
        $this->repositoryRegistry = $repositoryRegistry;
        $this->transactional = $transactional;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->uploader = $uploader;
    }

    /**
     * View diaries
     *
     * @Action("diary.search")
     *
     * @param DiarySearchRequest $request
     *
     * @return Diary[]
     */
    public function diaries(DiarySearchRequest $request)
    {
        $query = new DiaryQuery();

        if ($request->getFactoryIds()) {
            $searchFactoriesQuery = new SearchFactoriesByIdsQuery($request->getFactoryIds());
            $factories = $this->queryExecutor->execute($searchFactoriesQuery);
            $query->withFactories($factories);
        }

        if ($request->getClientIds()) {
            $searchClientsQuery = new SearchClientsByIdsQuery($request->getClientIds());
            $clients = $this->queryExecutor->execute($searchClientsQuery);
            $query->withClients($clients);
        }

        if ($request->getCreatorIds()) {
            $searchCreatorsQuery = new SearchUsersByIdsQuery($request->getCreatorIds());
            $creators = $this->queryExecutor->execute($searchCreatorsQuery);
            $query->withCreators($creators);
        }

        $pagination = $this->repositoryRegistry->getDiaryRepository()
            ->findBy($query, $request->getPage(), $request->getLimit());

        return $pagination;
    }

    /**
     * View diary
     *
     * @Action("diary")
     *
     * @param DiaryActionRequest $request
     *
     * @return Diary
     *
     * @throws DiaryNotFoundException
     */
    public function diary(DiaryActionRequest $request)
    {
        $diary = $this->repositoryRegistry->getDiaryRepository()
            ->find($request->getDiaryId());

        if (!$diary) {
            throw DiaryNotFoundException::withId($request->getDiaryId());
        }

        // @todo: check granted?

        return $diary;
    }

    /**
     * Create diary
     *
     * @Action("diary.create")
     *
     * @param DiaryCreateRequest $request
     *
     * @return Diary
     */
    public function create(DiaryCreateRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('DIARY_CREATE')) {
            throw new AccessDeniedException();
        }

        $creator = $this->tokenStorage->getToken()->getUser();

        $diary = $this->transactional->execute(function () use ($request, $creator) {
            $client = null;
            $money = null;

            if ($request->hasClient()) {
                $client = $this->loadClient($request->getClientId());
            }

            if ($request->hasMoney()) {
                $money = $this->createMoney($request->getMoney());
            }

            // Create diary object
            if ($client) {
                $diary = Diary::createForClient($creator, $client, $money);
            } else {
                $diary = Diary::create($creator, $money);
            }

            if ($request->hasFactories()) {
                $factories = $this->loadFactories($request->getFactoriesIds());
                $diary->replaceFactories($factories);
            }

            $diary
                ->setComment($request->getComment())
                ->setDocumentNumber($request->getDocumentNumber());

            if ($request->hasAttachments()) {
                foreach ($request->getAttachments() as $requestAttachment) {
                    $attachmentPath = $this->uploader->getTemporaryFilePath($requestAttachment->getPath());
                    $mimeType = MimeTypeGuesser::getInstance()->guess($attachmentPath);
                    $size = (new \SplFileInfo($attachmentPath))->getSize();
                    $webPath = $this->uploader->moveTemporaryFileToWebPath($requestAttachment->getPath());
                    $name = $requestAttachment->getName();

                    $attachment = new Attachment($webPath, $name, $size, $mimeType);
                    $diary->addAttachment($attachment);
                }
            }

            if ($request->getCatalogs()) {
                $catalogs = $this->loadCatalogs($request->getCatalogs());

                foreach ($catalogs as $catalog) {
                    $gotCatalog = new GotCatalog($catalog, $diary);
                    $this->repositoryRegistry->getGotCatalogRepository()
                        ->add($gotCatalog);
                }
            }

            $this->repositoryRegistry->getDiaryRepository()
                ->add($diary);

            return $diary;
        });

        return $diary;
    }

    /**
     * Update diary
     *
     * @Action("diary.update")
     *
     * @param DiaryUpdateRequest $request
     *
     * @throws DiaryNotFoundException
     */
    public function update(DiaryUpdateRequest $request)
    {
        $diary = $this->transactional->execute(function () use ($request) {
            $diary = $this->repositoryRegistry->getDiaryRepository()
                ->find($request->getDiaryId());

            if (!$diary) {
                throw DiaryNotFoundException::withId($request->getDiaryId());
            }

            if (!$this->authorizationChecker->isGranted('DIARY_EDIT', $diary)) {
                throw new AccessDeniedException();
            }

            $diary
                ->setComment($request->getComment())
                ->setDocumentNumber($request->getDocumentNumber());

            return $diary;
        });

        return $diary;
    }

    /**
     * Remove diary
     *
     * @Action("diary.remove")
     *
     * @param DiaryActionRequest $request
     */
    public function remove(DiaryActionRequest $request)
    {
        $diary = $this->transactional->execute(function () use ($request) {
            $diary = $this->repositoryRegistry->getDiaryRepository()
                ->find($request->getDiaryId());

            if (!$diary) {
                throw DiaryNotFoundException::withId($request->getDiaryId());
            }

            if (!$this->authorizationChecker->isGranted('DIARY_REMOVE', $diary)) {
                throw new AccessDeniedException();
            }

            $diary->remove();

            return $diary;
        });

        return $diary;
    }

    /**
     * Restore diary
     *
     * @Action("diary.restore")
     *
     * @param DiaryActionRequest $request
     */
    public function restore(DiaryActionRequest $request)
    {
        $diary = $this->transactional->execute(function () use ($request) {
            $diary = $this->repositoryRegistry->getDiaryRepository()
                ->find($request->getDiaryId());

            if (!$diary) {
                throw DiaryNotFoundException::withId($request->getDiaryId());
            }

            if (!$this->authorizationChecker->isGranted('DIARY_RESTORE', $diary)) {
                throw new AccessDeniedException();
            }

            $diary->restore();

            return $diary;
        });

        return $diary;
    }

    /**
     * Get catalogs for diary
     *
     * @Action("diary.got_catalogs")
     *
     * @param DiaryActionRequest $request
     *
     * @return GotCatalog[]
     */
    public function gotCatalogs(DiaryActionRequest $request)
    {
        $diary = $this->loadDiary($request->getDiaryId());

        $gotCatalogs = $this->repositoryRegistry->getGotCatalogRepository()
            ->findByDiary($diary);

        return $gotCatalogs;
    }

    /**
     * Get creators for diaries
     *
     * @Action("diary.creators")
     *
     * @return \AgentPlus\Entity\User\User[]
     */
    public function creators()
    {
        $query = new SearchCreatorsQuery();
        $creators = $this->queryExecutor->execute($query);

        return $creators;
    }

    /**
     * Load client
     *
     * @param string $id
     *
     * @return \AgentPlus\Entity\Client\Client
     *
     * @throws ClientNotFoundException
     */
    private function loadClient($id)
    {
        $client = $this->repositoryRegistry->getClientRepository()
            ->find($id);

        if (!$client) {
            throw ClientNotFoundException::withId($id);
        }

        return $client;
    }

    /**
     * Load factories
     *
     * @param array $factoryIds
     *
     * @return ArrayCollection|\AgentPlus\Entity\Factory\Factory[]
     *
     * @throws FactoryNotFoundException
     */
    private function loadFactories(array $factoryIds)
    {
        $factories = new ArrayCollection();

        foreach ($factoryIds as $factoryId) {
            $factory = $this->repositoryRegistry->getFactoryRepository()
                ->find($factoryId);

            if (!$factory) {
                throw FactoryNotFoundException::withId($factoryId);
            }

            $factories->add($factory);
        }

        return $factories;
    }

    /**
     * Load catalogs
     *
     * @param array $catalogIds
     *
     * @return ArrayCollection|\AgentPlus\Entity\Catalog\Catalog[]
     *
     * @throws CatalogNotFoundException
     */
    private function loadCatalogs(array $catalogIds)
    {
        $catalogs = new ArrayCollection();

        foreach ($catalogIds as $catalogId) {
            $catalog = $this->repositoryRegistry->getCatalogRepository()
                ->find($catalogId);

            if (!$catalog) {
                throw CatalogNotFoundException::withId($catalogId);
            }

            $catalogs[] = $catalog;
        }

        return $catalogs;
    }

    /**
     * Load diary by id
     *
     * @param string $id
     *
     * @return Diary
     *
     * @throws DiaryNotFoundException
     */
    public function loadDiary($id)
    {
        $diary = $this->repositoryRegistry->getDiaryRepository()->find($id);

        if (!$diary) {
            throw DiaryNotFoundException::withId($id);
        }

        return $diary;
    }

    /**
     * Create money
     *
     * @param MoneyRequest $money
     *
     * @return Money
     *
     * @throws CurrencyNotFoundException
     */
    private function createMoney(MoneyRequest $money)
    {
        $currency = $this->repositoryRegistry->getCurrencyRepository()
            ->find($money->getCurrency());

        if (!$currency) {
            throw CurrencyNotFoundException::withCode($money->getCurrency());
        }

        return new Money($currency, $money->getAmount());
    }
}
