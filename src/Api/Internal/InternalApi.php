<?php

namespace AgentPlus\Api\Internal;

use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Api\Response\Response;

class InternalApi
{
    /**
     * Ping action
     *
     * @Action(name="ping")
     */
    public function ping()
    {
        return new Response('pong');
    }
}
