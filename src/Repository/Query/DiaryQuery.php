<?php

namespace AgentPlus\Repository\Query;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Entity\Order\Stage;
use AgentPlus\Entity\User\User;
use AgentPlus\Query\DateTimeIntervalQuery;

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
     * @var array
     */
    private $countries = [];

    /**
     * @var array
     */
    private $cities = [];

    /**
     * @var DateTimeIntervalQuery
     */
    private $created;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->created = new DateTimeIntervalQuery();
    }

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

    /**
     * With country
     *
     * @param string $country
     *
     * @return DiaryQuery
     */
    public function withCountry($country)
    {
        $country = strtoupper($country);

        $this->countries[$country] = $country;

        return $this;
    }

    /**
     * With countries
     *
     * @param array $countries
     *
     * @return DiaryQuery
     */
    public function withCountries(array $countries)
    {
        $this->countries = [];

        foreach ($countries as $country) {
            $this->withCountry($country);
        }

        return $this;
    }

    /**
     * Has countries?
     *
     * @return bool
     */
    public function hasCountries()
    {
        return count($this->countries) > 0;
    }

    /**
     * Get countries
     *
     * @return array
     */
    public function getCountries()
    {
        return array_values($this->countries);
    }

    /**
     * With city
     *
     * @param string $city
     *
     * @return DiaryQuery
     */
    public function withCity($city)
    {
        $city = strtolower($city);

        $this->cities[$city] = $city;

        return $this;
    }

    /**
     * With cities
     *
     * @param array $cities
     *
     * @return DiaryQuery
     */
    public function withCities(array $cities)
    {
        $this->cities = [];

        foreach ($cities as $city) {
            $this->withCity($city);
        }

        return $this;
    }

    /**
     * Has cities?
     *
     * @return bool
     */
    public function hasCities()
    {
        return count($this->cities) > 0;
    }

    /**
     * Get cities
     *
     * @return array
     */
    public function getCities()
    {
        return array_values($this->cities);
    }

    /**
     * With created
     *
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return DiaryQuery
     */
    public function withCreated(\DateTime $from = null, \DateTime $to = null)
    {
        $this->created
            ->withFrom($from)
            ->withTo($to);

        return $this;
    }

    /**
     * Has created?
     *
     * @return bool
     */
    public function hasCreated()
    {
        return $this->created->hasFrom() || $this->created->hasTo();
    }

    /**
     * Get created
     *
     * @return DateTimeIntervalQuery
     */
    public function getCreated()
    {
        return $this->created;
    }
}
