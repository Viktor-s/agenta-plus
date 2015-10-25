<?php

namespace AgentPlus\Api\Internal\Client\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class ClientCreateRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    private $country;

    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    private $city;

    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    private $address;

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $phones;

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $emails;

    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    private $notes;

    /**
     * Get first name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get country code
     *
     * @return string
     */
    public function getCountryCode()
    {
        return $this->country;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Get phones
     *
     * @return array
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Get emails
     *
     * @return array
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
