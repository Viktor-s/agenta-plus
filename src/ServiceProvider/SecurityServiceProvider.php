<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Security\Provider\UserProvider;
use AgentPlus\Security\Voter\CatalogVoter;
use AgentPlus\Security\Voter\DiaryVoter;
use AgentPlus\Security\Voter\FactoryVoter;
use AgentPlus\Security\Voter\GotCatalogVoter;
use AgentPlus\Security\Voter\OrderVoter;
use AgentPlus\Security\Voter\StageVoter;
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

                'cabinet' => [
                    'pattern' => $app->getCabinetRequestMatcher(),
                    'anonymous' => true
                ]
            ]
        ]);

        $app['security.voters'] = $app->share($app->extend('security.voters', function($voters) {
            $voters[] = new TeamVoter();
            $voters[] = new FactoryVoter();
            $voters[] = new StageVoter();
            $voters[] = new DiaryVoter();
            $voters[] = new OrderVoter();
            $voters[] = new CatalogVoter();
            $voters[] = new GotCatalogVoter();

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
