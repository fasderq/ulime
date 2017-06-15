<?php
namespace Ulime\Backoffice\Article\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\Backoffice\Article\Model\Article;
use Ulime\Backoffice\Article\Repository\ArticleRepository;

/**
 * Class ArticleController
 * @package Ulime\Backoffice\Article\Controller
 */
class ArticleController
{
    protected $articlesRepository;

    /**
     * ArticleController constructor.
     */
    public function __construct()
    {
        $this->articlesRepository = new ArticleRepository();
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function articlesList(Application $app): Response
    {
        return $this->getHtmlResponse(
            $app,
            '/backoffice/article/article_list',
            [
                'articles' => $this->articlesRepository->getArticles()
            ]
        );
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function editArticle(Application $app, Request $request): Response
    {
        $articleName = $request->get('name');
        $isSubmitted = $request->get('data')['submit'];

        if ($isSubmitted) {
            $data = $request->get('data');
            $errors = $this->validateArticleFormData($data);

            if (empty($errors)) {
                $this->saveArticleFormData($data, $articleName);

                return new RedirectResponse('/backoffice/articles');
            }

        } elseif ($articleName) {
            $data = $this->getArticleFormData($articleName);
        }

        return $this->getHtmlResponse(
            $app,
            '/backoffice/article/article_edit',
            [
                'name' => $articleName,
                'data' => $data ?? [],
                'errors' => $errors ?? []
            ]
        );
    }

    /**
     * @param array $data
     * @param string|null $articleName
     */
    protected function saveArticleFormData(array $data, ?string $articleName = null): void
    {
        $article = new Article(
            $data['name'],
            $data['title'],
            $data['label'],
            $data['body']
        );

        if (!empty($articleName)) {
            $this->articlesRepository->editArticle($article, $articleName);
        } else {
            $this->articlesRepository->addArticle($article);
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteArticle(Request $request): RedirectResponse
    {
        $this->articlesRepository->deleteArticle($request->get('name'));

        return new RedirectResponse('/backoffice/articles');
    }

    /**
     * @param string $name
     * @return array
     */
    protected function getArticleFormData(string $name): array
    {
        $article = $this->articlesRepository->getArticleByName($name);

        return [
            'name' => $article->getName(),
            'title' => $article->getTitle(),
            'label' => $article->getLabel(),
            'body' => $article->getBody()
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function validateArticleFormData(array $data): array
    {
        $errors = [];

        if (empty(trim($data['name']))) {
            $errors['name'] = 'name is required';
        }

        if (empty(trim($data['title']))) {
            $errors['title'] = 'title is required';
        }

        if (empty(trim($data['label']))) {
            $errors['label'] = 'label is required';
        }

        if (empty(trim($data['body']))) {
            $errors['body'] = 'body is required';
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
