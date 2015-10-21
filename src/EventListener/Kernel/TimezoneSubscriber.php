<?php

namespace AgentPlus\EventListener\Kernel;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TimezoneSubscriber implements EventSubscriberInterface
{
    /**
     * Resolve client timezone
     *
     * @param GetResponseEvent $event
     */
    public function resolveClientTimezone(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $headers = $request->headers;

        if ($headers->has('X-Client-Timezone')) {
            $timezone = $headers->get('X-Client-Timezone');

            if(!@date_default_timezone_set($timezone)) {
                $response = new Response('Invalid time zone: ' . $timezone, 400);

                $event->setResponse($response);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                [ 'resolveClientTimezone', 512 ]
            ]
        ];
    }
}
