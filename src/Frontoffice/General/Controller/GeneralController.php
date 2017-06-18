<?php
namespace Ulime\Frontoffice\General\Controller;


use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\Frontoffice\General\Repository\GeneralRepository;
use Ulime\General\Renderer;

/**
 * Class GeneralController
 * @package Ulime\Frontoffice\General\Controller
 */
class GeneralController
{
    protected $renderer;
    protected $repository;

    /**
     * GeneralController constructor.
     * @param Renderer $renderer
     * @param GeneralRepository $generalRepository
     */
    public function __construct(
        Renderer $renderer,
        GeneralRepository $generalRepository
    ) {
        $this->renderer = $renderer;
        $this->repository = $generalRepository;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->renderer->getHtmlResponse(
            '/frontoffice/general/index',
            [
                'articles' => $this->repository->getPopularArticles()
            ],
            $request->getSession()
        );
    }

    /**
     * @return Response
     */
    public function sections(): Response
    {
        return $this->renderer->getHtmlResponse(
            '/frontoffice/general/catalog',
            [
                'sections' => $this->repository->getSections()
            ]
        );
    }


}
