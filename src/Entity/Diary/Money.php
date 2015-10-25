<?php

namespace AgentPlus\Entity\Diary;

use AgentPlus\Entity\Currency;
use AgentPlus\Entity\Money\AbstractMoney;
use Doctrine\ORM\Mapping as ORM;

/**
 * Money object
 *
 * @ORM\Embeddable()
 */
class Money extends AbstractMoney
{
    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $amount;

    /**
     * @var Currency
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Currency")
     * @ORM\JoinColumn(name="currency", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    private $currency;

    /**
     * Construct
     *
     * @param Currency $currency
     * @param float    $amount
     */
    public function __construct(Currency $currency = null, $amount)
    {
        $this->currency = $currency;
        $this->amount = $amount;
    }

    /**
     * Get currency
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
