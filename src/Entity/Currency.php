<?php

namespace AgentPlus\Entity;

use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="currency",
 *
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="currency_unique", columns={"code"})
 *      }
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\Currency")
 */
class Currency
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="code", type="string", length=3)
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @ModelTransform\Property()
     */
    private $code;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     *
     * @ModelTransform\Property()
     */
    private $position = 0;

    /**
     * Construct
     *
     * @param string $code
     * @param int    $position
     */
    public function __construct($code, $position = 0)
    {
        $this->code = $code;
        $this->position = $position;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return Currency
     */
    public function setPosition($position)
    {
        $this->position = $position;

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
}
