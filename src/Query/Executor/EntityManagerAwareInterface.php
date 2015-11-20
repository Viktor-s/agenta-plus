<?php

namespace AgentPlus\Query\Executor;

use Doctrine\ORM\EntityManagerInterface;

/**
 * @internal You can not use this interface in another components
 *           Allows use only in query executors.
 */
interface EntityManagerAwareInterface
{
    /**
     * Set entity manager
     *
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em);
}
