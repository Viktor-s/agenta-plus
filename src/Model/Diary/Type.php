<?php

namespace AgentPlus\Model\Diary;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

class Type
{
    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $id;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $parentId;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $name;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $fullName;

    /**
     * @var integer
     *
     * @ModelNormalize\Property()
     */
    private $level;

    /**
     * @var integer
     *
     * @ModelNormalize\Property()
     */
    private $position;

    /**
     * @var \AgentPlus\Model\Collection|Type[]
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $child;
}
