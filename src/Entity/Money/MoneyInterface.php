<?php

namespace AgentPlus\Entity\Money;

interface MoneyInterface
{
    /**
     * Get currency
     *
     * @return \AgentPlus\Entity\Currency
     */
    public function getCurrency();

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount();
}
