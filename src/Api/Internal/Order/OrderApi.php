<?php

namespace AgentPlus\Api\Internal\Order;

use AgentPlus\Api\Internal\Order\Request\OrderActionRequest;
use AgentPlus\Api\Internal\Order\Request\OrderCreateRequest;
use AgentPlus\Api\Internal\Order\Request\Money as MoneyRequest;
use AgentPlus\Api\Internal\Order\Request\OrderSearchRequest;
use AgentPlus\Api\Internal\Order\Request\OrderUpdateRequest;
use AgentPlus\Component\Uploader\Uploader;
use AgentPlus\Entity\Diary\Attachment;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\Order\Money as OrderMoney;
use AgentPlus\Entity\Order\Order;
use AgentPlus\Exception\Client\ClientNotFoundException;
use AgentPlus\Exception\Currency\CurrencyNotFoundException;
use AgentPlus\Exception\Factory\FactoryNotFoundException;
use AgentPlus\Exception\Order\OrderNotFoundException;
use AgentPlus\Exception\Order\StageNotFoundException;
use AgentPlus\Query\Client\SearchClientsByIdsQuery;
use AgentPlus\Query\Executor\QueryExecutor;
use AgentPlus\Query\Factory\SearchFactoriesByIdsQuery;
use AgentPlus\Query\Order\SearchCreatorsQuery;
use AgentPlus\Query\Stage\SearchStagesByIdsQuery;
use AgentPlus\Query\User\SearchUsersByIdsQuery;
use AgentPlus\Repository\Query\OrderQuery;
use AgentPlus\Repository\RepositoryRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrderApi
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
     * Search order
     *
     * @Action("order.search")
     *
     * @param OrderSearchRequest $request
     *
     * @return Order[]
     */
    public function orders(OrderSearchRequest $request)
    {
        $query = new OrderQuery();

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

        if ($request->getStageIds()) {
            $searchStagesQuery = new SearchStagesByIdsQuery($request->getStageIds());
            $stages = $this->queryExecutor->execute($searchStagesQuery);
            $query->withStages($stages);
        }

        if ($request->getCountryCodes()) {
            $query->withCountries($request->getCountryCodes());
        }

        if ($request->getCities()) {
            $query->withCities($request->getCities());
        }

        $created = $request->getCreated();
        $query->withCreated($created->getFrom(), $created->getTo());


        $orders = $this->repositoryRegistry->getOrderRepository()
            ->findBy($query, $request->getPage(), $request->getLimit());

        return $orders;
    }

    /**
     * View order
     *
     * @Action("order")
     *
     * @param OrderActionRequest $request
     *
     * @return Order
     */
    public function order(OrderActionRequest $request)
    {
        $order = $this->loadOrder($request->getId());

        if (!$this->authorizationChecker->isGranted('ORDER_VIEW', $order)) {
            throw new AccessDeniedException();
        }

        return $order;
    }

    /**
     * Create order
     *
     * @Action("order.create")
     *
     * @param OrderCreateRequest $request
     *
     * @return Order
     */
    public function create(OrderCreateRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('ORDER_CREATE')) {
            throw new AccessDeniedException();
        }

        $order = $this->transactional->execute(function () use ($request) {
            $creator = $this->tokenStorage->getToken()->getUser();
            $client = $this->loadClient($request->getClientId());
            $factory = $this->loadFactory($request->getFactoryId());
            $stage = $this->loadStage($request->getStageId());
            $orderMoney = $this->createOrderMoney($request->getMoney());

            // First step: create order
            $order = new Order($creator, $factory, $client, $orderMoney);

            $order->setStage($stage);

            // Second step: create diary
            $diary = Diary::createForOrder($creator, $order);
            $diary
                ->replaceFactories(new ArrayCollection([$factory]))
                ->setComment($request->getComment())
                ->setDocumentNumber($request->getDocumentNumber());

            foreach ($request->getAttachments() as $requestAttachment) {
                $attachmentPath = $this->uploader->getTemporaryFilePath($requestAttachment->getPath());
                $mimeType = MimeTypeGuesser::getInstance()->guess($attachmentPath);
                $size = (new \SplFileInfo($attachmentPath))->getSize();
                $webPath = $this->uploader->moveTemporaryFileToWebPath($requestAttachment->getPath());
                $name = $requestAttachment->getName();

                $attachment = new Attachment($webPath, $name, $size, $mimeType);
                $diary->addAttachment($attachment);
            }

            $this->repositoryRegistry->getOrderRepository()
                ->add($order);

            return $order;
        });

        return $order;
    }

    /**
     * Update order
     *
     * @Action("order.update")
     *
     * @param OrderUpdateRequest $request
     *
     * @return Order
     */
    public function update(OrderUpdateRequest $request)
    {
        $order = $this->transactional->execute(function () use ($request) {
            $order = $this->loadOrder($request->getId());

            if (!$this->authorizationChecker->isGranted('ORDER_EDIT', $order)) {
                throw new AccessDeniedException();
            }

            $creator = $this->tokenStorage->getToken()->getUser();
            $stage = $this->loadStage($request->getStageId());
            $orderMoney = $this->createOrderMoney($request->getMoney());

            $order
                ->setStage($stage)
                ->setMoney($orderMoney);

            $diary = Diary::createForOrder($creator, $order);
            $diary
                ->replaceFactories(new ArrayCollection([$order->getFactory()]))
                ->setComment($request->getComment())
                ->setDocumentNumber($request->getDocumentNumber());

            foreach ($request->getAttachments() as $requestAttachment) {
                $attachmentPath = $this->uploader->getTemporaryFilePath($requestAttachment->getPath());
                $mimeType = MimeTypeGuesser::getInstance()->guess($attachmentPath);
                $size = (new \SplFileInfo($attachmentPath))->getSize();
                $webPath = $this->uploader->moveTemporaryFileToWebPath($requestAttachment->getPath());
                $name = $requestAttachment->getName();

                $attachment = new Attachment($webPath, $name, $size, $mimeType);
                $diary->addAttachment($attachment);
            }

            return $order;
        });

        return $order;
    }

    /**
     * Get diaries for order
     *
     * @Action("order.diaries")
     *
     * @param OrderActionRequest $request
     *
     * @return Diary[]
     */
    public function diaries(OrderActionRequest $request)
    {
        $order = $this->loadOrder($request->getId());

        if (!$this->authorizationChecker->isGranted('ORDER_VIEW', $order)) {
            throw new AccessDeniedException();
        }

        return $order->getDiaries();
    }

    /**
     * Get creators for orders
     *
     * @Action("order.creators")
     *
     * @return \AgentPlus\Entity\User\User[]
     */
    public function creators()
    {
        $query = new SearchCreatorsQuery();
        $result = $this->queryExecutor->execute($query);

        return $result;
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
     * Load order
     *
     * @param string $id
     *
     * @return \AgentPlus\Entity\Order\Order
     *
     * @throws OrderNotFoundException
     */
    private function loadOrder($id)
    {
        $order = $this->repositoryRegistry->getOrderRepository()
            ->find($id);

        if (!$order) {
            OrderNotFoundException::withId($id);
        }

        return $order;
    }

    /**
     * Load stage
     *
     * @param string $id
     *
     * @return \AgentPlus\Entity\Order\Stage
     *
     * @throws StageNotFoundException
     */
    private function loadStage($id)
    {
        $stage = $this->repositoryRegistry->getStageRepository()
            ->find($id);

        if (!$stage) {
            throw StageNotFoundException::withId($id);
        }

        return $stage;
    }

    /**
     * Load factories
     *
     * @param array $id
     *
     * @return \AgentPlus\Entity\Factory\Factory
     *
     * @throws FactoryNotFoundException
     */
    private function loadFactory($id)
    {
        $factory = $this->repositoryRegistry->getFactoryRepository()
            ->find($id);

        if (!$factory) {
            throw FactoryNotFoundException::withId($id);
        }

        return $factory;
    }

    /**
     * Create order money
     *
     * @param MoneyRequest $money
     *
     * @return OrderMoney
     *
     * @throws CurrencyNotFoundException
     */
    private function createOrderMoney(MoneyRequest $money)
    {
        $currency = $this->repositoryRegistry->getCurrencyRepository()
            ->find($money->getCurrency());

        if (!$currency) {
            throw CurrencyNotFoundException::withCode($money->getCurrency());
        }

        return new OrderMoney($currency, $money->getAmount());
    }
}
