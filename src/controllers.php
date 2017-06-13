<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Ulime\Frontoffice\GeneralController;
use Ulime\Backoffice\CatalogController;
use Ulime\Backoffice\Section\Controller\SectionController;
use Ulime\Backoffice\Article\Controller\ArticleController;

//Request::setTrustedProxies(array('127.0.0.1'));

$action = function (string $className, string $method): string {
    return sprintf(
        '%s::%s',
        $className,
        $method
    );
};

$app->get('/', $action(GeneralController::class, 'index'))->bind('homepage');
$app->get('/catalog/{id}', $action(GeneralController::class, 'getCatalog'))->bind('catalog');
$app->get('/test', $action(GeneralController::class, 'getTest'));

//$app->get('/backoffice/test', $action(CatalogController::class, 'test'));
//$app->get('/backoffice/test',$action(SectionController::class, 'sectionList'));
//

$app->get('/backoffice/articles', $action(ArticleController::class, 'articlesList'));
$app->get('/backoffice/articles/{id}/edit', $action(ArticleController::class, 'editArticle'));



$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
