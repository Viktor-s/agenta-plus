<?php

namespace AgentPlus\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * Team entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="team",
 *
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="team_unique", columns={"keyword"})
 *      }
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\Team")
 */
class Team
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="team_sequence")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="keyword", type="string", length=32)
     *
     * @ModelTransform\Property()
     */
    private $key;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @ModelTransform\Property()
     */
    private $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="AgentPlus\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $owner;

    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     *
     * @ORM\ManyToMany(targetEntity="AgentPlus\Entity\User", inversedBy="teams")
     */
    private $users;

    /**
     * @var string
     *
     * @ORM\Column(name="name")
     *
     * @ModelTransform\Property()
     */
    private $name;

    /**
     * Construct
     *
     * @param User   $owner
     * @param string $name
     */
    public function __construct(User $owner, $name)
    {
        $this->users = new ArrayCollection();
        $this->owner = $owner;
        $this->name = $name;
        $this->createdAt = new \DateTime();
        $this->key = md5(uniqid(mt_rand(), true));
    }

    /**
     * Get key
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
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
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
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
     * Set name
     *
     * @param string $name
     *
     * @return Team
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection|User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add user to team
     *
     * @param User $user
     *
     * @return Team
     */
    public function addUser(User $user)
    {
        if (!$this->hasUser($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    /**
     * Has user
     *
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user)
    {
        return $this->users->exists(function ($key, User $userInTeam) use ($user) {
            return $user->getId() == $userInTeam->getId();
        });
    }

    /**
     * Remove user
     *
     * @param User $user
     *
     * @return Team
     */
    public function removeUser(User $user)
    {
        if ($this->hasUser($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }
}