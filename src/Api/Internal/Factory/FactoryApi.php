<?php

namespace AgentPlus\Api\Internal\Factory;

use AgentPlus\Api\Internal\Factory\Request\FactoryActionRequest;
use AgentPlus\Api\Internal\Factory\Request\FactoryCreateRequest;
use AgentPlus\Api\Internal\Factory\Request\FactorySearchRequest;
use AgentPlus\Api\Internal\Factory\Request\FactoryUpdateRequest;
use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Exception\Factory\FactoryNotFoundException;
use AgentPlus\Repository\FactoryRepository;
use AgentPlus\Repository\Query\FactoryQuery;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FactoryApi
{
    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

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
     * @param FactoryRepository             $factoryRepository
     * @param TransactionalInterface        $transactional
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        FactoryRepository $factoryRepository,
        TransactionalInterface $transactional,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->factoryRepository = $factoryRepository;
        $this->transactional = $transactional;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * View all factories
     *
     * @Action("factory.search")
     *
     * @param FactorySearchRequest $request
     *
     * @return Factory[]
     */
    public function factories(FactorySearchRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('FACTORY_LIST')) {
            throw new AccessDeniedException();
        }

        $query = new FactoryQuery();

        $pagination = $this->factoryRepository->findBy($query, $request->getPage(), $request->getLimit());

        return $pagination;
    }

    /**
     * View one factory
     *
     * @Action("factory")
     *
     * @param FactoryActionRequest $request
     *
     * @return Factory
     *
     * @throws FactoryNotFoundException
     */
    public function factory(FactoryActionRequest $request)
    {
        $factory = $this->factoryRepository->find($request->getId());

        if (!$factory) {
            throw FactoryNotFoundException::withId($request->getId());
        }

        return $factory;
    }

    /**
     * Create factory
     *
     * @Action("factory.create")
     *
     * @param FactoryCreateRequest $request
     *
     * @return Factory
     */
    public function create(FactoryCreateRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('FACTORY_CREATE')) {
            throw new AccessDeniedException();
        }

        $factory = new Factory($request->getName());

        $this->transactional->execute(function () use ($factory) {
            $this->factoryRepository->add($factory);
        });

        return $factory;
    }

    /**
     * Update factory
     *
     * @Action("factory.update")
     *
     * @param FactoryUpdateRequest $request
     *
     * @return Factory
     */
    public function update(FactoryUpdateRequest $request)
    {
        $factory = $this->transactional->execute(function () use ($request) {
            $factory = $this->factoryRepository->find($request->getId());

            if (!$factory) {
                throw FactoryNotFoundException::withId($request->getId());
            }

            if (!$this->authorizationChecker->isGranted('FACTORY_EDIT')) {
                throw new AccessDeniedException();
            }

            if ($request->hasName()) {
                $factory->setName($request->getName());
            }

            return $factory;
        });

        return $factory;
    }
}
