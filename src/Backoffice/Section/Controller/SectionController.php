<?php
namespace Ulime\Backoffice\Section\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ulime\Backoffice\Category\Repository\CategoryRepository;
use Ulime\Backoffice\Section\Repository\SectionRepository;

class SectionController
{
    protected $sectionRepository;
    protected $categoryRepository;

    public function __construct()
    {
        $this->sectionRepository = new SectionRepository();
        $this->categoryRepository = new CategoryRepository();
    }

    public function sectionsList(Application $app): Response
    {
        return $this->getHtmlResponse(
            $app,
            '/backoffice/section/section_list',
            [
                'sections' => $this->sectionRepository->getSections()
            ]
        );
    }

    public function editSection(Application $app, Request $request): Response
    {
        $sectionName = $request->get('name');

        if ($request->get('data')['submit']) {
//            $data  =
//            $errors =

        }

        return $this->getHtmlResponse(
            $app,
            '/backoffice/section/section_edit',
            [
                'name' => $sectionName,
                'data' => $data ?? [],
                'errors' => $errors ?? [],
                'categories' => $this->categoryRepository->getCategories()
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