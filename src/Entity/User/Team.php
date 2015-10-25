<?php

namespace AgentPlus\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FiveLab\Component\ModelTransformer\Annotation as ModelTransform;

/**
 * Team entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="team"
 * )
 *
 * @ModelTransform\Object(transformedClass="AgentPlus\Model\User\Team")
 */
class Team
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @ModelTransform\Property()
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @ModelTransform\Property()
     */
    private $createdAt;

    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     *
     * @ORM\ManyToMany(targetEntity="AgentPlus\Entity\User\User", inversedBy="teams")
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
     * @param string $name
     */
    public function __construct($name)
    {
        $this->users = new ArrayCollection();
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
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
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