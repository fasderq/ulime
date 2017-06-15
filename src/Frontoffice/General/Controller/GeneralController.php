<?php
namespace Ulime\Frontoffice\General\Controller;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\Frontoffice\General\Repository\GeneralRepository;

class GeneralController
{
    protected $repository;

    public function __construct()
    {
        $this->repository = new GeneralRepository();
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function index(Application $app): Response
    {
        return $this->getHtmlResponse(
            $app,
            '/frontoffice/general/index',
            [
                'articles' => $this->repository->getPopularArticles()
            ]
        );
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function catalogList(Application $app): Response
    {


        return $this->getHtmlResponse(
            $app,
            '/frontoffice/general/catalog/catalog_list',
            [

            ]
        );
    }

    public function catalog(Application $app, Request $request): Response
    {
        return $this->getHtmlResponse(
            $app,
            '/frontoffice/general/catalog/catalog_single',
            [

            ]
        );
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
