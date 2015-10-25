<?php

namespace AgentPlus\Repository;

use AgentPlus\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * User repository
 */
class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Add user to storage
     *
     * @param User $user
     */
    public function add(User $user)
    {
        $this->em->persist($user);
    }

    /**
     * Remove user from storage
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->em->remove($user);
    }

    /**
     * Find user by identifier
     *
     * @param integer $id
     *
     * @return User|null
     */
    public function findById($id)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find user by username
     *
     * @param string $username
     *
     * @return User|null
     */
    public function findByUsername($username)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.usernameCanonical = :username')
            ->setParameter('username', User::canonizeUsername($username))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find user by email
     *
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail($email)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.emailCanonical = :email')
            ->setParameter('email', User::canonizeEmail($email))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find user by username or email
     *
     * @param string $usernameOrEmail
     *
     * @return User|null
     */
    public function findByUsernameOrEmail($usernameOrEmail)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.usernameCanonical = :username OR u.emailCanonical = :email')
            ->setParameters([
                'username' => User::canonizeUsername($usernameOrEmail),
                'email' => User::canonizeEmail($usernameOrEmail)
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
