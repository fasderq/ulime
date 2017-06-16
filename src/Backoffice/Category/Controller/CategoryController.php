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

        return $this->getHtmlResponse(
            $app,
            '/backoffice/category/category_edit',
            [
                'id' => $categoryName,
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