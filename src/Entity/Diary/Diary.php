<?php

namespace AgentPlus\Entity\Diary;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Entity\Order\Order;
use AgentPlus\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @var \DateTime
     *
     * @ORM\Column(name="removed_at", type="datetime", nullable=true)
     */
    private $removedAt;

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
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=4, nullable=true)
     */
    private $amount;

    /**
     * @var \AgentPlus\Entity\Currency
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Currency")
     * @ORM\JoinColumn(name="currency", referencedColumnName="code", nullable=true, onDelete="RESTRICT")
     */
    private $currency;

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
        $this->setMoney(new Money(null, null));
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
        $diary->setMoney(new Money($orderMoney->getCurrency(), $orderMoney->getAmount()));

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
            $diary->setMoney($money);
        }

        return $diary;
    }

    /**
     * Base create diary
     *
     * @param User  $creator
     * @param Money $money
     *
     * @return Diary
     */
    public static function create(User $creator, Money $money = null)
    {
        $diary = new static($creator);

        if ($money) {
            $diary->setMoney($money);
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
     * Get creator
     *
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
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
     * Get factories
     *
     * @return \Doctrine\Common\Collections\Collection|Factory[]
     */
    public function getFactories()
    {
        return $this->factories;
    }

    /**
     * Has factory
     *
     * @param Factory $factory
     *
     * @return bool
     */
    public function hasFactory(Factory $factory)
    {
        return $this->factories->exists(function ($key, Factory $item) use ($factory) {
            return $factory->getId() == $item->getId();
        });
    }

    /**
     * Add factory
     *
     * @param Factory $factory
     *
     * @return Diary
     */
    public function addFactory(Factory $factory)
    {
        if (!$this->hasFactory($factory)) {
            $this->factories->add($factory);
        }

        return $this;
    }

    /**
     * Remove factory
     *
     * @param Factory $factory
     *
     * @return Diary
     */
    public function removeFactory(Factory $factory)
    {
        $index = null;

        $this->factories->forAll(function ($key, Factory $item) use ($factory, &$index) {
            if ($factory->getId() == $item->getId()) {
                $index = $key;

                return false;
            }

            return true;
        });

        $this->factories->remove($index);

        return $this;
    }

    /**
     * Replace factories
     *
     * @param Collection|Factory[] $factories
     *
     * @return Diary
     */
    public function replaceFactories(Collection $factories)
    {
        // First step: add factories
        foreach ($factories as $factory) {
            $this->addFactory($factory);
        }

        // Second step: remove factories
        foreach ($this->factories as $factory) {
            $exist = $factories->exists(function ($key, Factory $item) use ($factory) {
                return $item->getId() == $factory->getId();
            });

            if (!$exist) {
                $this->factories->removeElement($factory);
            }
        }

        return $this;
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
        $this->amount = $money->getAmount();
        $this->currency = $money->getCurrency();
        unset ($this->__money);

        return $this;
    }

    /**
     * Get money
     *
     * @return Money
     */
    public function getMoney()
    {
        if (isset($this->__money)) {
            return $this->__money;
        }

        $this->__money = new Money($this->currency, $this->amount);

        return $this->__money;
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

    /**
     * Remove diary
     *
     * @return Diary
     */
    public function remove()
    {
        $this->removedAt = new \DateTime();

        return $this;
    }

    /**
     * Restore
     *
     * @return Diary
     */
    public function restore()
    {
        $this->removedAt = null;

        return $this;
    }
}
