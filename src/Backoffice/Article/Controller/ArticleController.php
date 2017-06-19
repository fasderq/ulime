<?php
namespace Ulime\Backoffice\Article\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\Backoffice\Article\Model\Article;
use Ulime\Backoffice\Article\Repository\ArticleRepository;
use Ulime\Backoffice\User\Exception\ResponseException;
use Ulime\Backoffice\User\Service\SessionService;
use Ulime\Backoffice\User\Service\UserService;
use Ulime\General\Renderer;

/**
 * Class ArticleController
 * @package Ulime\Backoffice\Article\Controller
 */
class ArticleController
{
    protected $renderer;
    protected $userService;
    protected $sessionService;
    protected $articlesRepository;

    /**
     * ArticleController constructor.
     * @param Renderer $renderer
     * @param UserService $userService
     * @param SessionService $sessionService
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        Renderer $renderer,
        UserService $userService,
        SessionService $sessionService,
        ArticleRepository $articleRepository
    )
    {
        $this->renderer = $renderer;
        $this->userService = $userService;
        $this->sessionService = $sessionService;
        $this->articlesRepository = $articleRepository;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function articlesList(Request $request): Response
    {
        try {
            $this->sessionService->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        return $this->renderer->getHtmlResponse(
            '/backoffice/article/article_list',
            [
                'articles' => $this->articlesRepository->getArticles()
            ],
            $request->getSession()
        );
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function editArticle(Request $request): Response
    {
        try {
            $this->sessionService->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

        $articleName = $request->get('name');
        $isSubmitted = $request->get('data')['submit'];

        if ($isSubmitted) {
            $file = $request->files->get('data')['file'];

            $data = $request->get('data');
            $errors = $this->validateArticleFormData($data, $file ?? null);
            $data['file'] = $file;

            if (empty($errors)) {
                $this->saveArticleFormData($data, $articleName);

                return new RedirectResponse('/backoffice/articles');
            }

        } elseif ($articleName) {
            $data = $this->getArticleFormData($articleName);
        }

        return $this->renderer->getHtmlResponse(
            '/backoffice/article/article_edit',
            [
                'name' => $articleName,
                'data' => $data ?? [],
                'errors' => $errors ?? []
            ],
            $request->getSession()
        );
    }

    /**
     * @param array $data
     * @param string|null $articleName
     */
    protected function saveArticleFormData(array $data, ?string $articleName = null): void
    {
        $file = $data['file'];

        $type = explode('/', $file->getMimeType());

        $file->move(
            __DIR__ . '/../../../../web/images',
            $imgName = sprintf(
                '%s.%s',
                $data['name'],
                array_pop($type)
            )
        );

        $article = new Article(
            $data['name'],
            $data['title'],
            $data['label'],
            $data['body'],
            $imgName
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
        try {
            $this->sessionService->requireUserId($request->getSession());
        } catch (ResponseException $e) {
            return $e->getResponse();
        }

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
     * @param UploadedFile|null $file
     * @return array
     */
    protected function validateArticleFormData(array $data, ?UploadedFile $file = null): array
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

        $mimeType = $file->getClientMimeType();

        if (empty($file)) {
            $errors['image'] = 'image is required';
        } elseif ($file->getError()) {
            $errors['image_error'] = 'image error';
        } elseif ('image/jpeg' !== $mimeType && 'image/png' !== $mimeType) {
            $errors['image_format'] = 'image format must be .jpg or png';
        }

        return $errors;
    }
}
