<?php

use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Ulime\General\Providers\MongoDBServiceProvider;
use Ulime\General\Providers\RepositoryServiceProvider;
use Ulime\General\Providers\ControllerServiceProvider;
use Ulime\General\Providers\SessionServiceProvider;

$app = new Application();
$app->register(new ServiceControllerServiceProvider());
$app->register(new AssetServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new MongoDBServiceProvider());
$app->register(new ControllerServiceProvider());
$app->register(new RepositoryServiceProvider());
$app->register(new SessionServiceProvider());

return $app;
