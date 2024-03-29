<?php

namespace AgentPlus\Entity\Order;

use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * Stage entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="stage"
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\Order\Stage")
 */
class Stage
{
    /**
     * @var string
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
     * @ORM\Column(name="label", type="string")
     *
     * @ModelTransform\Property()
     */
    private $label;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     *
     * @ModelTransform\Property()
     */
    private $position;

    /**
     * Construct
     *
     * @param string $label
     * @param int    $position
     */
    public function __construct($label, $position = 0)
    {
        $this->label = $label;
        $this->position = $position;
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
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return string
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return Stage
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}
