<?php

namespace AgentPlus\Query\Executor;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @internal You can not use this trait in another system.
 *           Allows use only in query executors.
 */
trait EntityManagerAwareTrait
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * Set entity manager
     *
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
}
