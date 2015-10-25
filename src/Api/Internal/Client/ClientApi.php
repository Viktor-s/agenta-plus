<?php

namespace AgentPlus\Api\Internal\Client;

use AgentPlus\Api\Internal\Client\Request\ClientActionRequest;
use AgentPlus\Api\Internal\Client\Request\ClientCreateRequest;
use AgentPlus\Api\Internal\Client\Request\ClientSearchRequest;
use AgentPlus\Api\Internal\Client\Request\ClientUpdateRequest;
use AgentPlus\Entity\Client\Client;
use AgentPlus\Exception\Client\ClientNotFoundException;
use AgentPlus\Repository\ClientRepository;
use AgentPlus\Repository\Query\ClientQuery;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ClientApi
{
    /**
     * @var ClientRepository
     */
    private $clientRepository;

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
     * @param ClientRepository $clientRepository
     * @param TransactionalInterface $transactional
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        ClientRepository $clientRepository,
        TransactionalInterface $transactional,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->clientRepository = $clientRepository;
        $this->transactional = $transactional;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * View all clients
     *
     * @Action("client.search")
     *
     * @param ClientSearchRequest $request
     *
     * @return Client[]
     */
    public function clients(ClientSearchRequest $request)
    {
        $query = new ClientQuery();
        $clients = $this->clientRepository->findBy($query, $request->getPage(), $request->getLimit());

        return $clients;
    }

    /**
     * View client
     *
     * @Action("client")
     *
     * @param ClientActionRequest $request
     *
     * @return Client
     *
     * @throws ClientNotFoundException
     */
    public function client(ClientActionRequest $request)
    {
        $client = $this->clientRepository->find($request->getId());

        if (!$client) {
            throw ClientNotFoundException::withId($request->getId());
        }

        return $client;
    }

    /**
     * Create client
     *
     * @Action("client.create")
     *
     * @param ClientCreateRequest $request
     *
     * @return Client
     */
    public function create(ClientCreateRequest $request)
    {
        // @todo: check authorization

        $client = new Client($request->getName());
        $client
            ->setCountry($request->getCountryCode())
            ->setCity($request->getCity())
            ->setAddress($request->getAddress())
            ->setNotes($request->getNotes())
            ->setEmails($request->getEmails())
            ->setPhones($request->getPhones());

        $this->transactional->execute(function () use ($client) {
            $this->clientRepository->add($client);
        });

        return $client;
    }

    /**
     * Update client
     *
     * @Action("client.update")
     *
     * @param ClientUpdateRequest $request
     *
     * @return Client
     */
    public function update(ClientUpdateRequest $request)
    {
        $client = $this->transactional->execute(function () use ($request) {
            $client = $this->clientRepository->find($request->getId());

            if (!$client) {
                throw ClientNotFoundException::withId($request->getId());
            }

            if ($request->hasName()) {
                $client->setName($request->getName());
            }

            $client
                ->setCountry($request->getCountryCode())
                ->setCity($request->getCity())
                ->setAddress($request->getAddress())
                ->setNotes($request->getNotes())
                ->setAddress($request->getAddress())
                ->setPhones($request->getPhones())
                ->setEmails($request->getEmails());

            return $client;
        });

        return $client;
    }
}
