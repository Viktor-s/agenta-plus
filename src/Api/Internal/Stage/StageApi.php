<?php

namespace AgentPlus\Api\Internal\Stage;

use AgentPlus\Api\Internal\Stage\Request\StageCreateRequest;
use AgentPlus\Api\Internal\Stage\Request\StageUpdateRequest;
use AgentPlus\Entity\Order\Stage;
use AgentPlus\Exception\Order\StageNotFoundException;
use AgentPlus\Repository\StageRepository;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class StageApi
{
    /**
     * @var StageRepository
     */
    private $stageRepository;

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
     * @param StageRepository               $stageRepository
     * @param TransactionalInterface        $transactional
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        StageRepository $stageRepository,
        TransactionalInterface $transactional,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->stageRepository = $stageRepository;
        $this->transactional = $transactional;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * View all stages
     *
     * @Action("stage.search")
     *
     * @return Stage[]
     */
    public function stages()
    {
        return $this->stageRepository->findAll();
    }

    /**
     * Create stage
     *
     * @Action("stage.create")
     *
     * @param StageCreateRequest $request
     *
     * @return Stage
     */
    public function create(StageCreateRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('STAGE_CREATE')) {
            throw new AccessDeniedException();
        }

        $stage = new Stage($request->getLabel(), $request->getPosition());

        $this->transactional->execute(function () use ($stage) {
            $this->stageRepository->add($stage);
        });

        return $stage;
    }

    /**
     * Update stage
     *
     * @Action("stage.update")
     *
     * @param StageUpdateRequest $request
     *
     * @return Stage
     */
    public function update(StageUpdateRequest $request)
    {
        $stage = $this->transactional->execute(function () use ($request) {
            $stage = $this->stageRepository->find($request->getId());

            if (!$stage) {
                throw StageNotFoundException::withId($request->getId());
            }

            if (!$this->authorizationChecker->isGranted('STAGE_EDIT')) {
                throw new AccessDeniedException();
            }

            if ($request->hasPosition()) {
                $stage->setPosition($request->getPosition());
            }

            if ($request->hasName()) {
                $stage->setPosition($request->getPosition());
            }

            return $stage;
        });

        return $stage;
    }
}
