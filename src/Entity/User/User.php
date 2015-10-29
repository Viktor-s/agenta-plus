<?php

namespace AgentPlus\Entity\User;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User entity
 *
 * @ORM\Entity
 * @ORM\Table(
 *      name="users",
 *
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="users_username_canonical_unique", columns={"username_canonical"}),
 *          @ORM\UniqueConstraint(name="users_email_canonical_unique", columns={"email_canonical"})
 *      },
 *
 *      indexes={
 *          @ORM\Index(name="users_username_canonical_idx", columns={"username_canonical"}),
 *          @ORM\Index(name="users_email_canonical_idx", columns={"email_canonical"})
 *      }
 * )
 */
class User implements UserInterface
{
    const ROLE_DEFAULT      = 'ROLE_USER';

    const TYPE_AGENT    = 1;
    const TYPE_EMPLOYEE = 2;
    const TYPE_FACTORY  = 3;

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
     * @var Team
     *
     * @ORM\ManyToMany(targetEntity="AgentPlus\Entity\User\Team", mappedBy="users")
     * @ORM\JoinTable(
     *      name="team_users",
     *      joinColumns={
     *          @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *      },
     *      inverseJoinColumns={
     *          @ORM\JoinColumn(name="team_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *      }
     * )
     */
    private $teams;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="username_canonical", type="string")
     */
    private $usernameCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="email_canonical", type="string")
     */
    private $emailCanonical;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string")
     */
    private $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string")
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json_array")
     */
    private $roles = [];

    /**
     * @var string
     */
    private $plainPassword;

    /**
     * Construct
     *
     * @param int    $type
     * @param string $username
     * @param string $email
     * @param string $password
     */
    public function __construct($type, $username, $email, $password)
    {
        $availableTypes = static::getAvailableTypes();

        if (!in_array($type, $availableTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid user type "%s". Available types: "%s".',
                $type,
                implode('", "', $availableTypes)
            ));
        }

        $this->type = $type;

        $this->teams = new ArrayCollection();

        $this->createdAt = new \DateTime();
        $this->salt = md5(uniqid(mt_rand(), true));

        $this->email = $email;
        $this->emailCanonical = self::canonizeEmail($email);

        $this->username = $username;
        $this->usernameCanonical = self::canonizeUsername($username);

        $this->plainPassword = $password;
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
     * Get teams
     *
     * @return \Doctrine\Common\Collections\Collection|Team[]
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * Is agent?
     *
     * @return bool
     */
    public function isAgent()
    {
        return $this->type === self::TYPE_AGENT;
    }

    /**
     * Is employee?
     *
     * @return bool
     */
    public function isEmployee()
    {
        return $this->type === self::TYPE_EMPLOYEE;
    }

    /**
     * Is factory?
     *
     * @return bool
     */
    public function isFactory()
    {
        return $this->type === self::TYPE_FACTORY;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get canonical username
     *
     * @return string
     */
    public function getCanonicalUsername()
    {
        return $this->usernameCanonical;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Ger email canonical
     *
     * @return string
     */
    public function getEmailCanonical()
    {
        return $this->emailCanonical;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles)
    {
        $this->roles = [];
        array_map([$this, 'addRole'], $roles);

        return $this;
    }

    /**
     * Add role
     *
     * @param string $role
     *
     * @return User
     */
    public function addRole($role)
    {
        $role = strtoupper($role);

        if ($role == self::ROLE_DEFAULT) {
            return $this;
        }

        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;
        array_unshift($roles, self::ROLE_DEFAULT);

        return $roles;
    }

    /**
     * Has role
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        $role = strtoupper($role);

        if ($role == self::ROLE_DEFAULT) {
            return true;
        }

        return in_array($role, $this->roles);
    }

    /**
     * Remove role
     *
     * @param string $role
     *
     * @return User
     */
    public function removeRole($role)
    {
        $role = strtoupper($role);

        $this->roles = array_filter($this->roles, function ($value) use ($role){
            return $value !== $role;
        });

        return $this;
    }

    /**
     * Set plain password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;

        return $this;
    }

    /**
     * Get plain password
     *
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * {@inheritDoc}
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * Canonize username
     *
     * @param string $username
     *
     * @return string
     */
    public static function canonizeUsername($username)
    {
        return mb_strtolower($username);
    }

    /**
     * Canonize email
     *
     * @param string $email
     *
     * @return string
     */
    public static function canonizeEmail($email)
    {
        return mb_strtolower($email);
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getUsername() ?: '';
    }

    /**
     * Get available types
     *
     * @return array
     */
    public static function getAvailableTypes()
    {
        return [
            self::TYPE_AGENT,
            self::TYPE_FACTORY,
            self::TYPE_EMPLOYEE
        ];
    }
}
