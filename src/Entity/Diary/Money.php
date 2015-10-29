<?php

namespace AgentPlus\Entity\Diary;

use AgentPlus\Entity\Currency;
use AgentPlus\Entity\Money\AbstractMoney;
use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * Money object
 */
class Money extends AbstractMoney
{
    /**
     * @var float
     */
    private $amount;

    /**
     * @var \AgentPlus\Entity\Currency
     */
    private $currency;

    /**
     * Construct
     *
     * @param Currency $currency
     * @param float    $amount
     */
    public function __construct(Currency $currency = null, $amount = null)
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
