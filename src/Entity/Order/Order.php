<?php

namespace AgentPlus\Entity\Order;

use AgentPlus\Entity\Client\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="orders"
 * )
 */
class Order
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Client\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    private $client;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \Doctrine\Common\Collections\Collection|\AgentPlus\Entity\Diary\Diary[]
     *
     * @ORM\OneToMany(targetEntity="AgentPlus\Entity\Diary\Diary", mappedBy="order")
     */
    private $diaries;

    /**
     * @var Stage
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Order\Stage")
     * @ORM\JoinColumn(name="stage_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    private $stage;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="AgentPlus\Entity\Order\Money", columnPrefix="money_")
     */
    private $money;

    /**
     * Construct
     *
     * @param Client $client
     * @param Money  $money
     */
    public function __construct(Client $client, Money $money)
    {
        $this->client = $client;
        $this->money = $money;
        $this->createdAt = new \DateTime();
        $this->diaries = new ArrayCollection();
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
     * Get diaries
     *
     * @return \Doctrine\Common\Collections\Collection|\AgentPlus\Entity\Diary\Diary[]
     */
    public function getDiaries()
    {
        return $this->diaries;
    }

    /**
     * Get stage
     *
     * @return Stage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Get money
     *
     * @return Money
     */
    public function getMoney()
    {
        return $this->money;
    }
}
