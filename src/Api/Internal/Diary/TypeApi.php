<?php

namespace AgentPlus\Api\Internal\Diary;

use AgentPlus\Api\Internal\Diary\Request\TypeActionRequest;
use AgentPlus\Api\Internal\Diary\Request\TypeCreateRequest;
use AgentPlus\Api\Internal\Diary\Request\TypeSearchRequest;
use AgentPlus\Api\Internal\Diary\Request\TypeUpdateRequest;
use AgentPlus\Entity\Diary\Type;
use AgentPlus\Exception\Diary\DiaryTypeNotFoundException;
use AgentPlus\Query\Diary\SearchRootDiaryTypesQuery;
use AgentPlus\Query\Executor\QueryExecutor;
use AgentPlus\Repository\RepositoryRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Api\Response\ObjectTransformableResponse;
use FiveLab\Component\ModelNormalizer\Context as NormalizeContext;
use FiveLab\Component\ModelTransformer\Context as TransformContext;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TypeApi
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Construct
     *
     * @param QueryExecutor                 $queryExecutor
     * @param RepositoryRegistry            $repositoryRegistry
     * @param TransactionalInterface        $transactional
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        QueryExecutor $queryExecutor,
        RepositoryRegistry $repositoryRegistry,
        TransactionalInterface $transactional,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->queryExecutor = $queryExecutor;
        $this->repositoryRegistry = $repositoryRegistry;
        $this->transactional = $transactional;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * View one type
     *
     * @Action("diary.type")
     *
     * @param TypeActionRequest $request
     *
     * @return Type
     */
    public function type(TypeActionRequest $request)
    {
        $type = $this->findType($request->getTypeId());

        return $type;
    }

    /**
     * Get types
     *
     * @Action("diary.types")
     *
     * @param TypeSearchRequest $request
     *
     * @return ObjectTransformableResponse
     */
    public function types(TypeSearchRequest $request)
    {
        $searchTypesQuery = new SearchRootDiaryTypesQuery();
        $types = $this->queryExecutor->execute($searchTypesQuery);

        if ($request->isHierarchicalMode()) {
            $types = new ArrayCollection($types);

            $typeTransformContext = new TransformContext();
            $typeTransformContext->setAttribute('with_child', true);

            $transformContext = new TransformContext();
            $transformContext->setAttribute('child_context', $typeTransformContext);

            $normalizeContext = new NormalizeContext();

            $objectResponse = new ObjectTransformableResponse($types, $transformContext, $normalizeContext);

            return $objectResponse;
        }

        $realTypes = new ArrayCollection();

        $mergeTypes = function (Type $type, ArrayCollection $types) use (&$mergeTypes) {
            foreach ($type->getChild() as $childType) {
                $types->add($childType);
                $mergeTypes($childType, $types);
            }
        };

        foreach ($types as $type) {
            $realTypes->add($type);
            $mergeTypes($type, $realTypes);
        }

        return $realTypes;
    }

    /**
     * Create type
     *
     * @Action("diary.type.create")
     *
     * @param TypeCreateRequest $request
     *
     * @return Type
     */
    public function create(TypeCreateRequest $request)
    {
        if (!$this->authorizationChecker->isGranted('DIARY_TYPE_CREATE')) {
            throw new AccessDeniedException();
        }

        $parent = null;

        if ($request->getParent()) {
            $parent = $this->findType($request->getParent());
        }

        $type = new Type($request->getName(), $parent);

        $this->transactional->execute(function () use ($type) {
            $this->repositoryRegistry->getDiaryTypeRepository()->add($type);
        });

        return $type;
    }

    /**
     * Update type
     *
     * @Action("diary.type.update")
     *
     * @param TypeUpdateRequest $request
     *
     * @return Type
     */
    public function update(TypeUpdateRequest $request)
    {
        $type = $this->findType($request->getTypeId());

        if (!$this->authorizationChecker->isGranted('DIARY_TYPE_EDIT', $type)) {
            throw new AccessDeniedException();
        }

        $parent = null;

        if ($request->getParentId()) {
            $parent = $this->findType($request->getParentId());
        }

        $this->transactional->execute(function () use ($type, $parent, $request) {
            $type
                ->setName($request->getName())
                ->setParent($parent)
                ->setPosition($request->getPosition());
        });

        return $type;
    }

    /**
     * Find diary type by id
     *
     * @param string $id
     *
     * @return Type
     *
     * @throws DiaryTypeNotFoundException
     */
    private function findType($id)
    {
        $type = $this->repositoryRegistry->getDiaryTypeRepository()
            ->find($id);

        if (!$type) {
            throw DiaryTypeNotFoundException::withId($id);
        }

        return $type;
    }
}
