<?php
namespace Ulime\General\Providers;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Ulime\API\Controller\ApiController;
use Ulime\Backoffice\Article\Controller\ArticleController;
use Ulime\Backoffice\Category\Controller\CategoryController;
use Ulime\Backoffice\Section\Controller\SectionController;
use Ulime\Backoffice\User\Controller\AuthController;
use Ulime\Frontoffice\General\Controller\GeneralController;

class ControllerServiceProvider implements ServiceProviderInterface
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
        $app['backoffice.auth.controller'] = function () use ($app) {
            return new AuthController(
                $app['renderer'],
                $app['user.service'],
                $app['session.service']
            );
        };

        $app['backoffice.article.controller'] = function () use ($app) {
            return new ArticleController(
                $app['renderer'],
                $app['user.service'],
                $app['session.service'],
                $app['backoffice.article.repository']
            );
        };

        $app['backoffice.category.controller'] = function () use ($app) {
            return new CategoryController(
                $app['renderer'],
                $app['user.service'],
                $app['session.service'],
                $app['backoffice.category.repository'],
                $app['backoffice.article.repository']
            );
        };

        $app['backoffice.section.controller'] = function () use ($app) {
            return new SectionController(
                $app['renderer'],
                $app['backoffice.section.repository'],
                $app['backoffice.category.repository'],
                $app['session.service']
            );
        };

        $app['api.controller'] = function () use ($app) {
            return new ApiController(
                $app['api.repository']
            );
        };

        $app['frontoffice.general.controller'] = function () use ($app) {
            return new GeneralController(
                $app['renderer'],
                $app['frontoffice.general.repository']
            );
        };
    }
}
