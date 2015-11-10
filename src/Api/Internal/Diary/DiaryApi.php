<?php

namespace AgentPlus\Api\Internal\Diary;

use AgentPlus\Api\Internal\Diary\Request\DiaryActionRequest;
use AgentPlus\Api\Internal\Diary\Request\DiaryCreateRequest;
use AgentPlus\Api\Internal\Diary\Request\DiarySearchRequest;
use AgentPlus\Api\Internal\Diary\Request\DiaryUpdateRequest;
use AgentPlus\Api\Internal\Diary\Request\Money as MoneyRequest;
use AgentPlus\Component\Uploader\Uploader;
use AgentPlus\Entity\Diary\Attachment;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\Diary\Money;
use AgentPlus\Exception\Client\ClientNotFoundException;
use AgentPlus\Exception\Currency\CurrencyNotFoundException;
use AgentPlus\Exception\Diary\DiaryNotFoundException;
use AgentPlus\Exception\Factory\FactoryNotFoundException;
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
     * @param RepositoryRegistry            $repositoryRegistry
     * @param TransactionalInterface        $transactional
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
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
            ->find($request->getId());

        if (!$diary) {
            throw DiaryNotFoundException::withId($request->getId());
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
                ->find($request->getId());

            if (!$diary) {
                throw DiaryNotFoundException::withId($request->getId());
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
                ->find($request->getId());

            if (!$diary) {
                throw DiaryNotFoundException::withId($request->getId());
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
                ->find($request->getId());

            if (!$diary) {
                throw DiaryNotFoundException::withId($request->getId());
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
