<?php

namespace AgentPlus\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransformer;

/**
 * Invoice entity
 *
 * @ORM\Entity()
 * @ORM\Table(
 *      name="client_invoices",
 *      indexes={
 *          @ORM\Index(name="client_invoice_client_idx", columns={"client_id"})
 *      }
 * )
 *
 * @ModelTransformer\Object(transformedClass="AgentPlus\Model\Client\Invoice")
 */
class Invoice
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Client\Client", inversedBy="invoices")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $client;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * Construct
     *
     * @param Client $client
     * @param string $name
     */
    public function __construct(Client $client, $name)
    {
        $this->client = $client;
        $this->name = $name;
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Invoice
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
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
