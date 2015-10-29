<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @DataMapping\Object(allProperties=true)
 * @Assert\Callback({"checkCurrencyRequires"})
 */
class Money
{
    /**
     * @var float
     *
     * @Assert\Range(min = 0)
     */
    private $amount;

    /**
     * @var string
     *
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

    /**
     * Check currency requires
     *
     * @param ExecutionContextInterface $context
     */
    public function checkCurrencyRequires(ExecutionContextInterface $context)
    {
        if ($this->amount > 0 && !$this->currency) {
            $context->buildViolation('The currency is required.')
                ->atPath('currency')
                ->addViolation();
        }
    }
}
