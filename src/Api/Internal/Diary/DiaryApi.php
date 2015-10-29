<?php

namespace AgentPlus\Api\Internal\Diary;

use AgentPlus\Api\Internal\Diary\Request\DiaryActionRequest;
use AgentPlus\Api\Internal\Diary\Request\DiaryCreateRequest;
use AgentPlus\Api\Internal\Diary\Request\DiarySearchRequest;
use AgentPlus\Api\Internal\Diary\Request\DiaryUpdateRequest;
use AgentPlus\Api\Internal\Diary\Request\Money as MoneyRequest;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\Diary\Money;
use AgentPlus\Exception\Client\ClientNotFoundException;
use AgentPlus\Exception\Currency\CurrencyNotFoundException;
use AgentPlus\Exception\Diary\DiaryNotFoundException;
use AgentPlus\Exception\Factory\FactoryNotFoundException;
use AgentPlus\Repository\ClientRepository;
use AgentPlus\Repository\CurrencyRepository;
use AgentPlus\Repository\DiaryRepository;
use AgentPlus\Repository\FactoryRepository;
use AgentPlus\Repository\Query\DiaryQuery;
use Doctrine\Common\Collections\ArrayCollection;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DiaryApi
{
    /**
     * @var DiaryRepository
     */
    private $diaryRepository;

    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

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
     * Construct
     *
     * @param DiaryRepository               $diaryRepository
     * @param ClientRepository              $clientRepository
     * @param FactoryRepository             $factoryRepository
     * @param CurrencyRepository            $currencyRepository
     * @param TransactionalInterface        $transactional
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        DiaryRepository $diaryRepository,
        ClientRepository $clientRepository,
        FactoryRepository $factoryRepository,
        CurrencyRepository $currencyRepository,
        TransactionalInterface $transactional,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->diaryRepository = $diaryRepository;
        $this->clientRepository = $clientRepository;
        $this->factoryRepository = $factoryRepository;
        $this->currencyRepository = $currencyRepository;
        $this->transactional = $transactional;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
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

        $pagination = $this->diaryRepository->findBy($query, $request->getPage(), $request->getLimit());

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
        $diary = $this->diaryRepository->find($request->getId());

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

            $diary->setComment($request->getComment());

            $this->diaryRepository->add($diary);

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
            $diary = $this->diaryRepository->find($request->getId());

            if (!$diary) {
                throw DiaryNotFoundException::withId($request->getId());
            }

            if (!$this->authorizationChecker->isGranted('DIARY_EDIT', $diary)) {
                throw new AccessDeniedException();
            }

            $diary->setComment($request->getComment());

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
        $client = $this->clientRepository->find($id);

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
            $factory = $this->factoryRepository->find($factoryId);

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
        $currency = $this->currencyRepository->find($money->getCurrency());

        if (!$currency) {
            throw CurrencyNotFoundException::withCode($money->getCurrency());
        }

        return new Money($currency, $money->getAmount());
    }
}
