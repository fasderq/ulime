<?php
namespace Ulime\Backoffice\Section\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\Backoffice\Category\Model\Category;
use Ulime\Backoffice\Category\Repository\CategoryRepository;
use Ulime\General\Renderer;
use Ulime\Backoffice\Section\Model\Section;
use Ulime\Backoffice\Section\Repository\SectionRepository;
use Ulime\Backoffice\User\Exception\ResponseException;
use Ulime\Backoffice\User\Service\SessionService;

/**
 * Class SectionController
 * @package Ulime\Backoffice\Section\Controller
 */
class SectionController
{
    protected $renderer;
    protected $sectionRepository;
    protected $categoryRepository;
    protected $session;

    /**
     * SectionController constructor.
     * @param Renderer $renderer
     * @param SectionRepository $sectionRepository
     * @param CategoryRepository $categoryRepository
     * @param SessionService $sessionService
     */
    public function __construct(
        Renderer $renderer,
        SectionRepository $sectionRepository,
        CategoryRepository $categoryRepository,
        SessionService $sessionService
    ) {
        $this->renderer = $renderer;
        $this->sectionRepository = $sectionRepository;
        $this->categoryRepository = $categoryRepository;
        $this->session = $sessionService;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function sectionsList(Request $request): Response
    {
        try {
            $this->session->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        return $this->renderer->getHtmlResponse(
            '/backoffice/section/section_list',
            [
                'sections' => $this->sectionRepository->getSections()
            ],
            $request->getSession()
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editSection(Request $request): Response
    {
        try {
            $this->session->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        $sectionName = $request->get('name');

        if ($request->get('data')['submit']) {
            $data = $request->get('data');
            $errors = $this->validateSectionFormData($data);

            if (empty($errors)) {
                $this->saveSectionFormData($data, $sectionName);

                return new RedirectResponse('/backoffice/sections');
            }
        } elseif ($sectionName) {
            $data = $this->getSectionFormData($sectionName);
        }

        return $this->renderer->getHtmlResponse(
            '/backoffice/section/section_edit',
            [
                'name' => $sectionName,
                'data' => $data ?? [],
                'errors' => $errors ?? [],
                'categories' => $this->categoryRepository->getCategories()
            ],
            $request->getSession()
        );
    }

    /**
     * @param array $data
     * @param null|string $sectionName
     */
    protected function saveSectionFormData(array $data, ?string $sectionName): void
    {
        $categoryNames = $data['categories'] ?? [];

        $categories = [];
        foreach ($categoryNames as $categoryName => $category) {
            if ($category['is_active']) {
                $categories[] = $this->categoryRepository->getCategoryByName($categoryName);
            }
        }

        $section = new Section(
            $data['title'],
            $data['name'],
            $categories
        );

        if (!empty($sectionName)) {
            $this->sectionRepository->editSection($section, $sectionName);
        } else {
            $this->sectionRepository->addSection($section);
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteSection(Request $request): RedirectResponse
    {
        try {
            $this->session->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        $this->sectionRepository->deleteSection($request->get('name'));

        return new RedirectResponse('/backoffice/sections');
    }

    /**
     * @param string $sectionName
     * @return array
     */
    protected function getSectionFormData(string $sectionName): array
    {
        $section = $this->sectionRepository->getSectionByName($sectionName);

        return [
            'title' => $section->getTitle(),
            'name' => $section->getName(),
            'categories' => array_reduce(
                $section->getCategories(),
                function (array $categories, Category $category) {
                    return $categories + [
                        $category->getName() => [
                            'title' => $category->getTitle(),
                            'name' => $category->getName()
                        ]
                    ];
                },
                []
            )
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function validateSectionFormData(array $data): array
    {
        $errors = [];

        if (empty(trim($data['name']))) {
            $errors['name'] = 'name is required';
        }

        if (empty(trim($data['title']))) {
            $errors['title'] = 'title is required';
        }

        return $errors;
    }
}
