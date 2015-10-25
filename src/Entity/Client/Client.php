<?php

namespace AgentPlus\Entity\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Client entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="client"
 * )
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Client
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=2, nullable=true)
     */
    private $country;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

    /**
     * @var array
     *
     * @ORM\Column(name="phones", type="json_array")
     */
    private $phones = [];

    /**
     * @var array
     *
     * @ORM\Column(name="emails", type="json_array")
     */
    private $emails = [];

    /**
     * @var string
     *
     * @ORM\Column(name="notes", nullable=true)
     */
    private $notes;

    /**
     * @var \Doctrine\Common\Collections\Collection|Invoice[]
     *
     * @ORM\OneToMany(targetEntity="AgentPlus\Entity\Client\Invoice", mappedBy="client", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $invoices;

    /**
     * Construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->invoices = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Client
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set country code
     *
     * @param string $country
     *
     * @return Client
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return Client
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
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
     * Set address
     *
     * @param string $address
     *
     * @return Client
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
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
     * Set phones
     *
     * @param array $phones
     *
     * @return Client
     */
    public function setPhones(array $phones)
    {
        $this->phones = $phones;

        return $this;
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
     * Set emails
     *
     * @param array $emails
     *
     * @return Client
     */
    public function setEmails(array $emails)
    {
        $this->emails = $emails;

        return $this;
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
     * Set notes
     *
     * @param string $notes
     *
     * @return Client
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return array
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Get invoices
     *
     * @return \Doctrine\Common\Collections\Collection|Invoice[]
     */
    public function getInvoices()
    {
        return $this->invoices;
    }

    /**
     * Add invoice
     *
     * @param Invoice $invoice
     *
     * @return Client
     */
    public function addInvoice(Invoice $invoice)
    {
        if ($this->id) {
            if ($this->id != $invoice->getClient()->getId()) {
                throw new \InvalidArgumentException(sprintf(
                    'Can not add invoice from another client. Active client: "%s"; Client in invoice: "%s".',
                    $this->id,
                    $invoice->getClient()->getId()
                ));
            }
        }

        if (!$this->hasInvoice($invoice)) {
            $this->invoices->add($invoice);
        }

        return $this;
    }

    /**
     * Remove invoice
     *
     * @param Invoice $invoice
     *
     * @return Client
     */
    public function removeInvoice(Invoice $invoice)
    {
        if ($this->id) {
            if ($this->id != $invoice->getClient()->getId()) {
                throw new \InvalidArgumentException(sprintf(
                    'Can not remove invoice from another client. Active client: "%s"; Client in invoice: "%s".',
                    $this->id,
                    $invoice->getClient()->getId()
                ));
            }
        }

        if ($this->hasInvoice($invoice)) {
            $this->invoices->removeElement($invoice);
        }

        return $this;
    }

    /**
     * Has invoice
     *
     * @param Invoice $invoice
     *
     * @return bool
     */
    public function hasInvoice(Invoice $invoice)
    {
        return $this->invoices->exists(function ($key, Invoice $item) use ($invoice) {
            return $invoice->getId() == $item->getId();
        });
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ?: '';
    }

    /**
     * On update
     *
     * @ORM\PreUpdate
     */
    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
