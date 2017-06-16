<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\Backoffice\Section\Controller\SectionController;
use Ulime\Backoffice\Article\Controller\ArticleController;
use Ulime\Backoffice\Category\Controller\CategoryController;
use Ulime\API\Controller\ApiController;
use Ulime\Frontoffice\General\Controller\GeneralController;

//Request::setTrustedProxies(array('127.0.0.1'));

$action = function (string $className, string $method): string {
    return sprintf(
        '%s::%s',
        $className,
        $method
    );
};

/*Frontoffice*/

$app->get('/', $action(GeneralController::class, 'index'));



/*Backoffice*/

/*Articles*/
$app->get('/backoffice/articles', $action(ArticleController::class, 'articlesList'));
$app->get('/backoffice/article/{name}/edit', $action(ArticleController::class, 'editArticle'));
$app->post('/backoffice/article/{name}/edit', $action(ArticleController::class, 'editArticle'));
$app->get('/backoffice/article/{name}/delete', $action(ArticleController::class, 'deleteArticle'));

/*Categories*/
$app->get('/backoffice/categories', $action(CategoryController::class, 'categoryList'));
$app->get('/backoffice/category/{name}/edit', $action(CategoryController::class, 'editCategory'));
$app->post('/backoffice/category/{name}/edit', $action(CategoryController::class, 'editCategory'));
$app->get('/backoffice/category/{name}/delete', $action(CategoryController::class, 'deleteCategory'));

/*Sections*/
$app->get('/backoffice/sections', $action(SectionController::class, 'sectionsList'));
$app->get('/backoffice/section/{name}/edit', $action(SectionController::class, 'editSection'));
$app->post('/backoffice/section/{name}/edit', $action(SectionController::class, 'editSection'));
$app->get('/backoffice/section/{name}/delete', $action(SectionController::class, 'deleteSection'));

/*API*/
$app->get('/categories', $action(ApiController::class, 'getCategories'));
$app->get('/category/{name}', $action(ApiController::class, 'getArticles'));


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
