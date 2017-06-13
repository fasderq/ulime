<?php
namespace Ulime\Frontoffice;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GeneralController
 * @package Ulime
 */
class GeneralController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new Repository();
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function index(Application $app): Response
    {
        return $this->getHtmlResponse($app, 'index', []);
    }

    /**
     * @param Request $request
     * @param Application $app
     * @return Response
     */
    public function getCatalog(Request $request, Application $app): Response
    {
        $catalogId = (int)$request->get('id');

        return $this->getHtmlResponse(
            $app,
            '/public/catalog',
            [
                'catalog' => $catalogId
            ]
        );
    }


    public function getTest(Request $request)
    {
        $test = $this->repository->find();
        var_dump($test);

        return new JsonResponse(['test' => 1]);
    }

    /**
     * @param Application $app
     * @param string $template
     * @param array $content
     * @return Response
     */
    protected function getHtmlResponse(Application $app, string $template, array $content = []): Response
    {
        return new Response(
            $app['twig']->render(
                sprintf(
                    '%s%s',
                    $template,
                    '.html.twig'
                ),
                $content
            )
        );
    }
}
