<?php

namespace AgentPlus\Entity\Factory;

use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * Factory entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="factory"
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\Factory\Factory")
 */
class Factory
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @ModelTransform\Property()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     *
     * @ModelTransform\Property()
     */
    private $name;

    /**
     * Construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Factory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
