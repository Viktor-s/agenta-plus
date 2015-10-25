<?php

namespace AgentPlus\Entity\Diary;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Order\Order;
use AgentPlus\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Diary entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="diary"
 * )
 */
class Diary
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\User\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $creator;

    /**
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Order\Order", inversedBy="diaries")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    private $order;

    /**
     * @var \Doctrine\Common\Collections\Collection|\AgentPlus\Entity\Factory\Factory[]
     *
     * @ORM\ManyToMany(targetEntity="AgentPlus\Entity\Factory\Factory")
     * @ORM\JoinTable(
     *      name="diary_factories",
     *      joinColumns={
     *          @ORM\JoinColumn(name="diary_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="factory_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *      }
     * )
     */
    private $factories;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Client\Client")
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    private $client;

    /**
     * @var \AgentPlus\Entity\Order\Stage
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Order\Stage")
     * @ORM\JoinColumn(name="stage_id", referencedColumnName="id", nullable=true, onDelete="RESTRICT")
     */
    private $stage;

    /**
     * @var Money
     *
     * @ORM\Embedded(class="AgentPlus\Entity\Diary\Money", columnPrefix="money_")
     */
    private $money;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     */
    private $comment;

    /**
     * Construct
     *
     * @param User $creator
     */
    private function __construct(User $creator)
    {
        $this->creator = $creator;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->factories = new ArrayCollection();
        $this->money = new Money(null, null);
    }

    /**
     * Create a new daily for order
     *
     * @param User  $creator
     * @param Order $order
     *
     * @return Diary
     */
    public static function createForOrder(User $creator, Order $order)
    {
        $diary = new static($creator);

        $diary->stage = $order->getStage();
        $diary->client = $order->getClient();
        $diary->order = $order;

        $orderMoney = $order->getMoney();
        $diary->money = new Money($orderMoney->getCurrency(), $orderMoney->getAmount());

        return $diary;
    }

    /**
     * Create for client
     *
     * @param User   $creator
     * @param Client $client
     * @param Money  $money
     *
     * @return Diary
     */
    public static function createForClient(User $creator, Client $client, Money $money = null)
    {
        $diary = new static($creator);
        $diary->client = $client;

        if ($money) {
            $diary->money = $money;
        }

        return $diary;
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
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get factories
     *
     * @return \Doctrine\Common\Collections\Collection|\AgentPlus\Entity\Factory\Factory[]
     */
    public function getFactories()
    {
        return $this->factories;
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
     * Set money
     *
     * @param Money $money
     *
     * @return Diary
     */
    public function setMoney(Money $money)
    {
        $this->money = $money;

        return $this;
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

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Diary
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
