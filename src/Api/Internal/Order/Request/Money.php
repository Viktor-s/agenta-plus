<?php

namespace AgentPlus\Api\Internal\Order\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class Money
{
    /**
     * @var float
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 0)
     */
    private $amount;

    /**
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Currency
     */
    private $currency;

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
