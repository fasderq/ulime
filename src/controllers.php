<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

//Request::setTrustedProxies(array('127.0.0.1'));

/*Frontoffice*/

$app->get('/', 'frontoffice.general.controller:index');
$app->get('/sections', 'frontoffice.general.controller:sections');



/*Backoffice*/

/*Auth*/

$app->get('/backoffice', 'backoffice.auth.controller:index');
$app->get('/backoffice/login', 'backoffice.auth.controller:login');
$app->post('/backoffice/login', 'backoffice.auth.controller:login');
$app->get('/backoffice/logout', 'backoffice.auth.controller:logout');

/*Articles*/
$app->get('/backoffice/articles', 'backoffice.article.controller:articlesList');
$app->get('/backoffice/article/{name}/edit', 'backoffice.article.controller:editArticle');
$app->post('/backoffice/article/{name}/edit', 'backoffice.article.controller:editArticle');
$app->get('/backoffice/article/{name}/delete', 'backoffice.article.controller:deleteArticle');

/*Categories*/
$app->get('/backoffice/categories', 'backoffice.category.controller:categoryList');
$app->get('/backoffice/category/{name}/edit', 'backoffice.category.controller:editCategory');
$app->post('/backoffice/category/{name}/edit', 'backoffice.category.controller:editCategory');
$app->get('/backoffice/category/{name}/delete', 'backoffice.category.controller:deleteCategory');

/*Sections*/
$app->get('/backoffice/sections', 'backoffice.section.controller:sectionsList');
$app->get('/backoffice/section/{name}/edit', 'backoffice.section.controller:editSection');
$app->post('/backoffice/section/{name}/edit', 'backoffice.section.controller:editSection');
$app->get('/backoffice/section/{name}/delete', 'backoffice.section.controller:deleteSection');

/*API*/
$app->get('/api/categories', 'api.controller:getCategories');
$app->get('/api/category/{name}', 'api.controller:getArticles');


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
