<?php

namespace AgentPlus\Repository\Query;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Entity\Order\Stage;
use AgentPlus\Entity\User\User;

class DiaryQuery
{
    /**
     * @var array|Factory[]
     */
    private $factories = [];

    /**
     * @var array|User[]
     */
    private $creators = [];

    /**
     * @var array|Stage[]
     */
    private $stages;

    /**
     * @var array|Client[]
     */
    private $clients = [];

    /**
     * With factory
     *
     * @param Factory $factory
     *
     * @return DiaryQuery
     */
    public function withFactory(Factory $factory)
    {
        $this->factories[$factory->getId()] = $factory;

        return $this;
    }

    /**
     * With factories
     *
     * @param array|Factory[] $factories
     *
     * @return DiaryQuery
     */
    public function withFactories(array $factories)
    {
        $this->factories = [];

        foreach ($factories as $factory) {
            $this->withFactory($factory);
        }

        return $this;
    }

    /**
     * Has factories?
     *
     * @return bool
     */
    public function hasFactories()
    {
        return count($this->factories) > 0;
    }

    /**
     * Get factories
     *
     * @return array|Factory[]
     */
    public function getFactories()
    {
        return array_values($this->factories);
    }

    /**
     * With creator
     *
     * @param User $creator
     *
     * @return DiaryQuery
     */
    public function withCreator(User $creator)
    {
        $this->creators[$creator->getId()] = $creator;

        return $this;
    }

    /**
     * With creators
     *
     * @param array|User[] $creators
     *
     * @return DiaryQuery
     */
    public function withCreators(array $creators)
    {
        $this->creators = [];

        foreach ($creators as $creator) {
            $this->withCreator($creator);
        }

        return $this;
    }

    /**
     * Has creators?
     *
     * @return bool
     */
    public function hasCreators()
    {
        return count($this->creators) > 0;
    }

    /**
     * Get creators
     *
     * @return array|User[]
     */
    public function getCreators()
    {
        return array_values($this->creators);
    }

    /**
     * With stage
     *
     * @param Stage $stage
     *
     * @return DiaryQuery
     */
    public function withStage(Stage $stage)
    {
        $this->stages[$stage->getId()] = $stage;

        return $this;
    }

    /**
     * With stages
     *
     * @param array|Stage[] $stages
     *
     * @return DiaryQuery
     */
    public function withStages(array $stages)
    {
        $this->stages = [];

        foreach ($stages as $stage) {
            $this->withStage($stage);
        }

        return $this;
    }

    /**
     * Has stages?
     *
     * @return bool
     */
    public function hasStages()
    {
        return count($this->stages) > 0;
    }

    /**
     * Get stages
     *
     * @return array|Stage[]
     */
    public function getStages()
    {
        return array_values($this->stages);
    }

    /**
     * With client
     *
     * @param Client $client
     *
     * @return DiaryQuery
     */
    public function withClient(Client $client)
    {
        $this->clients[$client->getId()] = $client;

        return $this;
    }

    /**
     * With clients
     *
     * @param array|Client[] $clients
     *
     * @return DiaryQuery
     */
    public function withClients(array $clients)
    {
        $this->clients = [];

        foreach ($clients as $client) {
            $this->withClient($client);
        }

        return $this;
    }

    /**
     * Has clients
     *
     * @return bool
     */
    public function hasClients()
    {
        return count($this->clients) > 0;
    }

    /**
     * Get clients
     *
     * @return array|Client[]
     */
    public function getClients()
    {
        return array_values($this->clients);
    }
}
