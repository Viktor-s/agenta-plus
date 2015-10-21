<?php

namespace AgentPlus\EventListener\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AddAllowOriginSubscriber implements EventSubscriberInterface
{
    /**
     * @var RequestMatcher
     */
    private $apiRequestMatcher;

    /**
     * @var string
     */
    private $cpHost;

    /**
     * Construct
     *
     * @param RequestMatcher $apiRequestMatcher
     * @param string         $cpHost
     */
    public function __construct(RequestMatcher $apiRequestMatcher, $cpHost)
    {
        $this->apiRequestMatcher = $apiRequestMatcher;
        $this->cpHost = $cpHost;
    }

    /**
     * Add allow origin header on API request
     *
     * @param FilterResponseEvent $event
     */
    public function addAllowOriginHeaderOnApiRequest(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if ($this->apiRequestMatcher->matches($request)) {
            $response = $event->getResponse();

            $response->headers->set('Access-Control-Allow-Origin', [
                'http://' . $this->cpHost
            ], false);

            $response->headers->set('Access-Control-Allow-Headers', [
                'Origin, X-Requested-With, Content-Type, Accept, Authorization, X-Client-Timezone'
            ], false);

            $response->headers->set('Access-Control-Allow-Methods', [
                'POST, GET, OPTIONS'
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                'addAllowOriginHeaderOnApiRequest'
            ]
        ];
    }
}
