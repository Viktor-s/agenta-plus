<?php

namespace AgentPlus\Api\External;

use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Api\Response\Response;
use Symfony\Component\Intl\Intl;

class CountryApi
{
    /**
     * Country list
     *
     * @Action("countries")
     *
     * @return Response
     */
    public function countries()
    {
        $regionBundle = Intl::getRegionBundle();
        $regions = $regionBundle->getCountryNames();
        $countries = [];

        foreach ($regions as $countryCode => $countryName) {
            $countries[] = [
                'code' => $countryCode,
                'name' => $countryName
            ];
        }

        return new Response($countries);
    }
}
