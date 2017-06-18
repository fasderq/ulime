<?php
namespace Ulime\General\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use MongoDB\Client;

/**
 * Class MongoDBServiceProvider
 * @package Ulime
 */
class MongoDBServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $app
     */
    public function register(Container $app)
    {
        $app['mongodb'] = function ($app) {
            $config = [
                'server' => 'mongodb://localhost:27017',
                'options' => [],
                'driverOptions' => [],
            ];
            if (!isset($app['mongodb.config'])) {
                $app['mongodb.config'] = [];
            }
            foreach ($config as $k => $v) {
                if (isset($app['mongodb.config'][$k])) {
                    $config[$k] = $app['mongodb.config'][$k];
                }
            }
            $app['mongodb.config'] = $config;
            $client = new Client(
                $app['mongodb.config']['server'],
                $app['mongodb.config']['options'],
                $app['mongodb.config']['driverOptions']
            );

            return $client;
        };
    }
}