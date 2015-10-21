<?php

namespace AgentPlus\EventListener\Kernel;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TimeAndMemoryDebugSubscriber implements EventSubscriberInterface
{
    /**
     * @var float
     */
    private $startTime;

    /**
     * @var float
     */
    private $requestedTime;

    /**
     * On request
     */
    public function onRequest()
    {
        $this->startTime = microtime(true);
    }

    /**
     * On exception
     */
    public function onException()
    {
        $this->requestedTime = microtime(true) - $this->startTime;
    }

    /**
     * On response
     *
     * @param FilterResponseEvent $event
     */
    public function onResponse(FilterResponseEvent $event)
    {
        if (!$this->requestedTime) {
            $this->requestedTime = microtime(true) - $this->startTime;
        }

        $headers = $event->getResponse()->headers;
        $headers->set('X-Requested-Time', round($this->requestedTime, 3) . 's');
        $headers->set('X-Memory-Usage', round(memory_get_usage() / (1024 * 1024), 3) . ' Mb');
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequest', 2048]
            ],

            KernelEvents::EXCEPTION => [
                ['onException']
            ],

            KernelEvents::RESPONSE => [
                ['onResponse', -2048]
            ]
        ];
    }
}
