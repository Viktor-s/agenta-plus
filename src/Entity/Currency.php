<?php

namespace AgentPlus\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="currency",
 *
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="currency_unique", columns={"code"})
 *      }
 * )
 */
class Currency
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="code", type="string", length=3)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $code;

    /**
     * Construct
     *
     * @param string $code
     */
    public function __construct($code)
    {
        $this->code = $code;
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
}
