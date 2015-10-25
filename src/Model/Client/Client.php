<?php

namespace AgentPlus\Model\Client;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

class Client
{
    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $name;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $countryCode;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $countryName;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $city;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $address;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $notes;

    /**
     * @var array
     *
     * @ModelNormalize\Property()
     */
    private $phones;

    /**
     * @var array
     *
     * @ModelNormalize\Property()
     */
    private $emails;

    /**
     * @var \AgentPlus\Model\Collection|\AgentPlus\Model\Client\Invoice[]
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $invoices;
}
