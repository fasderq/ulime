<?php
namespace Ulime\General\Providers;


use MongoDB\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Ulime\Backoffice\Article\Repository\ArticleRepository;
use Ulime\Backoffice\Category\Repository\CategoryRepository;
use Ulime\Backoffice\Section\Repository\SectionRepository;
use Ulime\Frontoffice\General\Repository\GeneralRepository;

class RepositoryServiceProvider implements ServiceProviderInterface
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
        $app['mongo.client'] = function () use ($app) {
            return new Client();
        };

        $app['backoffice.article.repository'] = function () use ($app) {
            return new ArticleRepository($app['mongo.client']);
        };

        $app['backoffice.category.repository'] = function () use ($app) {
            return new CategoryRepository($app['mongo.client']);
        };

        $app['backoffice.section.repository'] = function () use ($app) {
            return new SectionRepository($app['mongo.client']);
        };

        $app['frontoffice.general.repository'] = function () use ($app) {
            return new GeneralRepository($app['mongo.client']);
        };
    }
}