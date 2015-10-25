<?php

namespace AgentPlus\Api\Internal\Team;

use AgentPlus\Api\Internal\Team\Request\TeamActionRequest;
use AgentPlus\Api\Internal\Team\Request\TeamCreateRequest;
use AgentPlus\Api\Internal\Team\Request\TeamUpdateRequest;
use AgentPlus\Entity\User\Team;
use AgentPlus\Exception\Team\TeamNotFoundException;
use AgentPlus\Repository\TeamRepository;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Api\Response\StatusResponse;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TeamApi
{
    /**
     * @var TeamRepository
     */
    private $teamRepository;

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
     * @param TeamRepository                $teamRepository
     * @param TransactionalInterface        $transactional
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        TeamRepository $teamRepository,
        TransactionalInterface $transactional,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->teamRepository = $teamRepository;
        $this->transactional = $transactional;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Create team
     *
     * @Action("team.create")
     *
     * @param TeamCreateRequest $request
     *
     * @return Team
     */
    public function create(TeamCreateRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('TEAM_CREATE')) {
            throw new AccessDeniedException();
        }

        $owner = $this->tokenStorage->getToken()->getUser();
        $team = new Team($owner, $request->getName());

        $this->transactional->execute(function () use ($team) {
            $this->teamRepository->add($team);
        });

        return $team;
    }

    /**
     * List teams
     *
     * @Action("team.list")
     *
     * @return Team[]
     *
     * @throws AccessDeniedException
     */
    public function teams()
    {
        if (!$this->authorizationChecker->isGranted('TEAM_LIST')) {
            throw new AccessDeniedException();
        }

        $owner = $this->tokenStorage->getToken()->getUser();
        $teams = $this->teamRepository->findByOwner($owner);

        return $teams;
    }

    /**
     * Update team
     *
     * @Action("team.update")
     *
     * @param TeamUpdateRequest $request
     *
     * @return Team
     *
     * @throws TeamNotFoundException
     * @throws AccessDeniedException
     */
    public function update(TeamUpdateRequest $request)
    {
        $team = $this->transactional->execute(function () use ($request) {
            $team = $this->findTeam($request->getTeamId());

            if (!$this->authorizationChecker->isGranted('EDIT', $team)) {
                throw new AccessDeniedException();
            }

            if ($request->hasName()) {
                $team->setName($request->getName());
            }

            return $team;
        });

        return $team;
    }

    /**
     * Remove team
     *
     * @Action("team.remove")
     *
     * @param TeamActionRequest $request
     *
     * @return StatusResponse
     *
     * @throws TeamNotFoundException
     * @throws AccessDeniedException
     */
    public function remove(TeamActionRequest $request)
    {
        $this->transactional->execute(function () use ($request) {
            $team = $this->findTeam($request->getTeamId());

            if (!$this->authorizationChecker->isGranted('REMOVE', $team)) {
                throw new AccessDeniedException();
            }

            $this->teamRepository->remove($team);
        });

        return new StatusResponse(true);
    }

    /**
     * Find team
     *
     * @param string $id
     *
     * @return Team
     *
     * @throws TeamNotFoundException
     */
    public function findTeam($id)
    {
        $team = $this->teamRepository->find($id);

        if (!$team) {
            throw TeamNotFoundException::withId($id);
        }

        return $team;
    }
}
