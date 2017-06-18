<?php
namespace Ulime\Frontoffice\General\Controller;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\Frontoffice\General\Repository\GeneralRepository;

/**
 * Class GeneralController
 * @package Ulime\Frontoffice\General\Controller
 */
class GeneralController
{
    protected $repository;

    /**
     * GeneralController constructor.
     * @param GeneralRepository $generalRepository
     */
    public function __construct(GeneralRepository $generalRepository)
    {
        $this->repository = $generalRepository;
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
    public function sections(Application $app): Response
    {
        return $this->getHtmlResponse(
            $app,
            '/frontoffice/general/catalog',
            [
                'sections' => $this->repository->getSections()
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
