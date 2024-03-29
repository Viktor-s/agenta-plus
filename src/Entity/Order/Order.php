<?php

namespace AgentPlus\Entity\Order;

use AgentPlus\Entity\Client\Client;
use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\Factory\Factory;
use AgentPlus\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\Reflection\Reflection;

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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\User\User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    private $creator;

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
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var \Doctrine\Common\Collections\Collection|Factory[]
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Factory\Factory")
     * @ORM\JoinColumn(name="factory_id", referencedColumnName="id", nullable=false, onDelete="RESTRICT")
     */
    private $factory;

    /**
     * @var \Doctrine\Common\Collections\Collection|Diary[]
     *
     * @ORM\OneToMany(targetEntity="AgentPlus\Entity\Diary\Diary", mappedBy="order", cascade={"persist"})
     * @ORM\OrderBy({"createdAt": "DESC"})
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
     * @var float
     *
     * @ORM\Column(name="amount", type="decimal", precision=10, scale=4)
     */
    private $amount;

    /**
     * @var \AgentPlus\Entity\Currency
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\Currency")
     * @ORM\JoinColumn(name="currency", referencedColumnName="code", onDelete="RESTRICT")
     */
    private $currency;

    /**
     * Construct
     *
     * @param User    $creator
     * @param Factory $factory
     * @param Client  $client
     * @param Money   $money
     */
    public function __construct(User $creator, Factory $factory, Client $client, Money $money)
    {
        $this->creator = $creator;
        $this->factory = $factory;
        $this->client = $client;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->diaries = new ArrayCollection();
        $this->setMoney($money);
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
     * Get client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
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
     * Get factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Add diary
     *
     * @param Diary $diary
     *
     * @return Order
     */
    public function addDiary(Diary $diary)
    {
        if ($this->getId() && $diary->getId() && $order = Reflection::getPropertyValue($diary, 'order')) {
            throw new \RuntimeException(sprintf(
                'The diary "%s" have a another order "%s".',
                $diary->getId(),
                $order->getId()
            ));
        }

        Reflection::setPropertyValue($diary, 'order', $this);

        $this->diaries->add($diary);

        return $this;
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
     * Set stage
     *
     * @param Stage $stage
     *
     * @return Order
     */
    public function setStage(Stage $stage)
    {
        $this->stage = $stage;

        return $this;
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
     * Set money
     *
     * @param Money $money
     *
     * @return Order
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
}
