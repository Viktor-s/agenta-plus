<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Security\Provider\UserProvider;
use AgentPlus\Security\Voter\FactoryVoter;
use AgentPlus\Security\Voter\TeamVoter;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\SecurityServiceProvider as SilexSecurityServiceProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class SecurityServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app->register(new SilexSecurityServiceProvider(), [
            'security.encoder_factory' => $app->share(function (){
                return new EncoderFactory([
                    'AgentPlus\Entity\User' => new MessageDigestPasswordEncoder('sha512', true, 100),
                    'Symfony\Component\Security\Core\User\UserInterface' => new MessageDigestPasswordEncoder()
                ]);
            }),

            'security.firewalls' => [
                'api_external' => [
                    'pattern' => $app->getApiExternalRequestMatcher(),
                    'anonymous' => true
                ],

                'api_internal' => [
                    'pattern' => $app->getApiInternalRequestMatcher(),
                    'http' => true,
                    'users' => $app->share(function (AppKernel $kernel) {
                        return new UserProvider($kernel->getUserRepository());
                    })
                ],

                'main' => [
                    'pattern' => $app->getMainRequestMatcher(),
                    'anonymous' => true
                ]
            ]
        ]);

        $app['security.voters'] = $app->share($app->extend('security.voters', function($voters) {
            $voters[] = new TeamVoter();
            $voters[] = new FactoryVoter();

            return $voters;
        }));
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
