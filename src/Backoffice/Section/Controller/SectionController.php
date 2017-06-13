<?php
namespace Ulime\Backoffice\Section\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ulime\Backoffice\Section\Repository\SectionRepository;

class SectionController
{
    protected $sectionRepository;

    public function __construct()
    {
        $this->sectionRepository = new SectionRepository();
    }

    public function sectionList(Application $app): Response
    {
        $sections = $this->sectionRepository->getSections();


        return $this->getHtmlResponse(
            $app,
            '/backoffice/section/section_list',
            [

            ]
        );
    }

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