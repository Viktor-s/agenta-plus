<?php

namespace AgentPlus\Repository;

class RepositoryRegistry
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var DiaryRepository
     */
    private $diaryRepository;

    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var StageRepository
     */
    private $stageRepository;

    /**
     * @var TeamRepository
     */
    private $teamRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Get client repository
     *
     * @return ClientRepository
     */
    public function getClientRepository()
    {
        return $this->clientRepository;
    }

    /**
     * Get currency repository
     *
     * @return CurrencyRepository
     */
    public function getCurrencyRepository()
    {
        return $this->currencyRepository;
    }

    /**
     * Get diary repository
     *
     * @return DiaryRepository
     */
    public function getDiaryRepository()
    {
        return $this->diaryRepository;
    }

    /**
     * Get factory repository
     *
     * @return FactoryRepository
     */
    public function getFactoryRepository()
    {
        return $this->factoryRepository;
    }

    /**
     * Get order repository
     *
     * @return OrderRepository
     */
    public function getOrderRepository()
    {
        return $this->orderRepository;
    }

    /**
     * Get stage repository
     *
     * @return StageRepository
     */
    public function getStageRepository()
    {
        return $this->stageRepository;
    }

    /**
     * Get team repository
     *
     * @return TeamRepository
     */
    public function getTeamRepository()
    {
        return $this->teamRepository;
    }

    /**
     * Get user repository
     *
     * @return UserRepository
     */
    public function getUserRepository()
    {
        return $this->userRepository;
    }
}