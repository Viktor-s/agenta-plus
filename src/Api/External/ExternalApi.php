<?php

namespace AgentPlus\Api\External;

use FiveLab\Component\Api\Annotation\Action;
use FiveLab\Component\Api\Response\Response;

class ExternalApi
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
