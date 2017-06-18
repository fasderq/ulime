<?php
namespace Ulime\Backoffice\Category\Controller;


use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\Backoffice\Article\Model\Article;
use Ulime\Backoffice\Article\Repository\ArticleRepository;
use Ulime\Backoffice\Category\Model\Category;
use Ulime\Backoffice\Category\Repository\CategoryRepository;
use Ulime\Backoffice\User\Exception\ResponseException;
use Ulime\Backoffice\User\Service\SessionService;
use Ulime\Backoffice\User\Service\UserService;
use Ulime\General\Renderer;

/**
 * Class CategoryController
 * @package Ulime\Backoffice\Category\Controller
 */
class CategoryController
{
    protected $renderer;
    protected $userService;
    protected $sessionService;
    protected $categoryRepository;
    protected $articleRepository;

    /**
     * CategoryController constructor.
     * @param Renderer $renderer
     * @param UserService $userService
     * @param SessionService $sessionService
     * @param CategoryRepository $categoryRepository
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        Renderer $renderer,
        UserService $userService,
        SessionService $sessionService,
        CategoryRepository $categoryRepository,
        ArticleRepository $articleRepository
    ) {
        $this->renderer = $renderer;
        $this->userService = $userService;
        $this->sessionService = $sessionService;
        $this->categoryRepository = $categoryRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function categoryList(Request $request): Response
    {
        try {
            $this->sessionService->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        return $this->renderer->getHtmlResponse(
            '/backoffice/category/category_list',
            [
                'categories' => $this->categoryRepository->getCategories()
            ],
            $request->getSession()
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editCategory(Request $request): Response
    {
        try {
            $this->sessionService->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        $categoryName = $request->get('name');

        if ($request->get('data')['submit']) {
            $data = $request->get('data');
            $errors = $this->validateCategoryFormData($data);

            if (empty($errors)) {
                $this->saveCategoryFormData($data, $categoryName);

                return new RedirectResponse('/backoffice/categories');
            }
        } elseif ($categoryName) {
            $data = $this->getCategoryFormData($categoryName);
        }

        return $this->renderer->getHtmlResponse(
            '/backoffice/category/category_edit',
            [
                'id' => $categoryName,
                'errors' => $errors ?? [],
                'data' => $data ?? [],
                'articles' => $this->articleRepository->getArticles()
            ],
            $request->getSession()
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteCategory(Request $request): RedirectResponse
    {
        try {
            $this->sessionService->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        $this->categoryRepository->deleteCategory($request->get('name'));

        return new RedirectResponse('/backoffice/categories');
    }

    /**
     * @param array $data
     * @param null|string $categoryName
     */
    protected function saveCategoryFormData(array $data, ?string $categoryName = null): void
    {
        $articleNames = $data['articles'] ?? [];

        $articles = [];
        foreach ($articleNames as $articleName => $article) {
            if ($article['is_active']) {
                $articles[] = $this->articleRepository->getArticleByName($articleName);
            }
        }

        $category = new Category(
            $data['title'],
            $data['name'],
            $articles
        );

        if (!empty($categoryName)) {
            $this->categoryRepository->editCategory($category, $categoryName);
        } else {
            $this->categoryRepository->addCategory($category);
        }
    }

    /**
     * @param string $name
     * @return array
     */
    protected function getCategoryFormData(string $name): array
    {
        $category = $this->categoryRepository->getCategoryByName($name);

        return [
            'title' => $category->getTitle(),
            'name' => $category->getName(),
            'articles' => array_reduce(
                $category->getArticles(),
                function (array $articles, Article $article) {
                       return $articles + [
                            $article->getName() => [
                                'name' => $article->getName(),
                                'title' => $article->getTitle(),
                                'label' => $article->getLabel(),
                                'body' => $article->getBody()
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
    protected function validateCategoryFormData(array $data): array
    {
        $errors = [];

        if (empty(trim($data['title']))) {
            $errors['title'] = 'title is required';
        }

        if (empty(trim($data['name']))) {
            $errors['name'] = 'name is required';
        }

        return $errors;
    }
}