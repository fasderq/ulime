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
        $articles = $this->articlesRepository->getArticles();

        return $this->getHtmlResponse(
            $app,
            '/backoffice/article/article_list',
            [
                'articles' => $articles
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
        $articleId = $request->get('id');
        $isSubmitted = $request->get('data')['submit'];

        if ($isSubmitted) {
            $data = $request->get('data');
            $errors = $this->validateArticleFormData($data);

            if (empty($errors)) {
                $this->saveArticleFormData($data, $articleId);
                return new RedirectResponse('/backoffice/articles');
            }

        } elseif ($articleId) {
            $data = $this->getArticleFormData($articleId);
        }

        return $this->getHtmlResponse(
            $app,
            '/backoffice/article/article_edit',
            [
                'id' => $articleId,
                'data' => $data ?? [],
                'errors' => $errors ?? []
            ]
        );
    }

    /**
     * @param array $data
     * @param string|null $articleId
     */
    protected function saveArticleFormData(array $data, ?string $articleId = null): void
    {
        $article = new Article(
            $data['title'],
            $data['label'],
            $data['body']
        );

        if (!empty($articleId)) {
            $this->articlesRepository->editArticle($article, $articleId);
        } else {
            $this->articlesRepository->addArticle($article);
        }
    }

    /**
     * @param string $id
     * @return array
     */
    protected function getArticleFormData(string $id): array
    {
        $article = $this->articlesRepository->getArticleById($id);

        return [
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
