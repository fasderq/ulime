<?php
namespace Ulime\General;


use Silex\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ulime\Backoffice\User\Service\SessionService;
use Ulime\Backoffice\User\Service\UserService;

/**
 * Class Renderer
 * @package Ulime\Backoffice
 */
class Renderer
{
    protected $app;
    protected $twig;
    protected $sessionService;
    protected $userService;

    /**
     * Renderer constructor.
     * @param Application $app
     * @param \Twig_Environment $twig
     * @param SessionService $sessionService
     * @param UserService $userService
     */
    public function __construct(
        Application $app,
        \Twig_Environment $twig,
        SessionService $sessionService,
        UserService $userService
    ) {
        $this->app = $app;
        $this->twig = $twig;
        $this->sessionService = $sessionService;
        $this->userService = $userService;
    }

    /**
     * @param string $template
     * @param array $context
     * @param SessionInterface|null $session
     * @return Response
     */
    public function getHtmlResponse(string $template, array $context = [], SessionInterface $session = null): Response
    {
        return new Response(
            $this->render($template, $context, $session)
        );
    }

    /**
     * @param string $template
     * @param array $context
     * @param SessionInterface|null $session
     * @return string
     */
    protected function render(string $template, array $context = [], SessionInterface $session = null): string
    {
        $globalContext = [];
        if (null !== $session && $this->sessionService->isAuthenticated($session)) {
            $userId = $this->sessionService->requireUserId($session);
            $globalContext += [
                'sessionUser' => $this->userService->getUser($userId),
            ];
        }

        return $this->twig->render(
            sprintf(
            '%s%s',
            $template,
            '.html.twig'
            ),
            $context + $globalContext
        );
    }
}
