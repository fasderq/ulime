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

/**
 * Class CategoryController
 * @package Ulime\Backoffice\Category\Controller
 */
class CategoryController
{
    protected $categoryRepository;
    protected $articleRepository;

    /**
     * CategoryController constructor.
     */
    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
        $this->articleRepository = new ArticleRepository();
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function categoryList(Application $app): Response
    {
        return $this->getHtmlResponse(
            $app,
            '/backoffice/category/category_list',
            [
                'categories' => $this->categoryRepository->getCategories()
            ]
        );
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function editCategory(Application $app, Request $request): Response
    {
        $categoryId = $request->get('id');
        $isSubmitted = $request->get('data')['submit'];

        if ($isSubmitted) {
            $data = $request->get('data');
            $errors = $this->validateCategoryFormData($data);

            if (empty($errors)) {
                $this->saveCategoryFormData($data, $categoryId);

                return new RedirectResponse('/backoffice/categories');
            }
        } elseif ($categoryId) {
            $data = $this->getCategoryFormData($categoryId);
        }

        return $this->getHtmlResponse(
            $app,
            '/backoffice/category/category_edit',
            [
                'id' => $categoryId,
                'errors' => $errors ?? [],
                'data' => $data ?? [],
                'articles' => $this->articleRepository->getArticles()
            ]
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteCategory(Request $request): RedirectResponse
    {
        $this->categoryRepository->deleteCategory($request->get('id'));

        return new RedirectResponse('/backoffice/categories');
    }

    /**
     * @param array $data
     * @param null|string $categoryId
     */
    protected function saveCategoryFormData(array $data, ?string $categoryId = null): void
    {
        $articlesIds = $data['articles'] ?? [];

        $articles = [];
        foreach ($articlesIds as $articleId => $article) {
            if (1 === (int)$article['is_active']) {
                $articles[$articleId] = $this->articleRepository->getArticleById($articleId);
            }
        }

        $category = new Category(
            $data['title'],
            $data['name'],
            $articles
        );

        if (!empty($categoryId)) {
            $this->categoryRepository->editCategory($category, $categoryId);
        } else {
            $this->categoryRepository->addCategory($category);
        }
    }

    /**
     * @param string $id
     * @return array
     */
    protected function getCategoryFormData(string $id): array
    {
        $category = $this->categoryRepository->getCategoryById($id);

        $articles = [];
        foreach ($category->getArticles() as $articleId => $article) {
            $articles[$articleId] = array_reduce(
                [$article],
                function (array $row, Article $article) {
                    return $row + [
                        'title' => $article->getTitle(),
                        'label' => $article->getLabel(),
                        'body' => $article->getBody()
                    ];
                },
                []
            );
        }

        return [
            'title' => $category->getTitle(),
            'name' => $category->getName(),
            'articles' => $articles
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