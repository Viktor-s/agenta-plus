<?php

namespace AgentPlus\Api\Internal\Order;

use AgentPlus\Api\Internal\Order\Request\OrderCreateRequest;
use AgentPlus\Api\Internal\Order\Request\Money as MoneyRequest;
use AgentPlus\Component\Uploader\Uploader;
use AgentPlus\Entity\Diary\Attachment;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\Order\Money;
use AgentPlus\Entity\Order\Order;
use AgentPlus\Exception\Client\ClientNotFoundException;
use AgentPlus\Exception\Currency\CurrencyNotFoundException;
use AgentPlus\Exception\Factory\FactoryNotFoundException;
use AgentPlus\Exception\Order\StageNotFoundException;
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
     * Create order
     *
     * @Action("order.create")
     *
     * @param OrderCreateRequest $request
     */
    public function create(OrderCreateRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('ORDER_CREATE')) {
            throw new AccessDeniedException();
        }

        $order = $this->transactional->execute(function () use ($request) {
            $creator = $this->tokenStorage->getToken()->getUser();
            $client = $this->loadClient($request->getClientId());
            $factories = $this->loadFactories($request->getFactoryIds());
            $stage = $this->loadStage($request->getStageId());
            $money = $this->createMoney($request->getMoney());

            // First step: create diary
            $diary = Diary::createForClient($creator, $client, $money);
            $diary->replaceFactories($factories);

            foreach ($request->getAttachments() as $requestAttachment) {
                $attachmentPath = $this->uploader->getTemporaryFilePath($requestAttachment->getPath());
                $mimeType = MimeTypeGuesser::getInstance()->guess($attachmentPath);
                $size = (new \SplFileInfo($attachmentPath))->getSize();
                $webPath = $this->uploader->moveTemporaryFileToWebPath($requestAttachment->getPath());
                $name = $requestAttachment->getName();

                $attachment = new Attachment($webPath, $name, $size, $mimeType);
                $diary->addAttachment($attachment);
            }

            // First step: create order
            $order = new Order($creator, $client, $money);

            $order
                ->setStage($stage)
                ->addDiary($diary);

            $this->repositoryRegistry->getOrderRepository()
                ->add($order);
        });

        return $order;
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
