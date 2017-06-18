<?php

namespace Ulime\General\Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Ulime\Backoffice\User\Service\SessionService;
use Ulime\Backoffice\User\Service\UserService;
use Ulime\General\Renderer;

class SessionServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $app A container instance
     */
    public function register(Container $app)
    {
        $app['user.service'] = function () {
            return new UserService();
        };

        $app['session.service'] = function () {
            return new SessionService();
        };

        $app['renderer'] = function () use ($app) {
            return new Renderer(
                new Application,
                $app['twig'],
                $app['session.service'],
                $app['user.service']
            );
        };
    }
}
