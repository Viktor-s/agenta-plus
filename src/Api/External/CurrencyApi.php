<?php

namespace AgentPlus\Api\External;

use AgentPlus\Repository\CurrencyRepository;
use FiveLab\Component\Api\Annotation\Action;

class CurrencyApi
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * Construct
     *
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * View all currencies
     *
     * @Action("currencies")
     *
     * @return \AgentPlus\Entity\Currency[]
     */
    public function currencies()
    {
        $currencies = $this->currencyRepository->findAll();

        return $currencies;
    }
}
