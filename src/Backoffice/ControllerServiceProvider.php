<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16.06.17
 * Time: 14:08
 */

namespace Ulime\Backoffice;


use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Ulime\Backoffice\Article\Controller\ArticleController;
use Ulime\Backoffice\Category\Controller\CategoryController;
use Ulime\Backoffice\Section\Controller\SectionController;

class ControllerServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $app)
    {
        $app['backoffice.article.controller'] = function () use ($app) {
            return new ArticleController($app['backoffice.article.repository']);
        };

        $app['backoffice.category.controller'] = function () use ($app) {
            return new CategoryController(
                $app['backoffice.category.repository'],
                $app['backoffice.article.repository']
            );
        };

        $app['backoffice.section.controller'] = function () use ($app) {
            return new SectionController(
                $app['backoffice.section.repository'],
                $app['backoffice.category.repository']
            );
        };
    }
}
