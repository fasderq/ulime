<?php
namespace Ulime\Backoffice\User\Controller;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\General\Renderer;
use Ulime\Backoffice\User\Exception\AuthenticationFailure;
use Ulime\Backoffice\User\Service\SessionService;
use Ulime\Backoffice\User\Service\UserService;

/**
 * Class AuthController
 * @package Ulime\Backoffice\User\Controller
 */
class AuthController
{
    protected $renderer;
    protected $userService;
    protected $sessionService;

    /**
     * AuthController constructor.
     * @param Renderer $renderer
     * @param UserService $userService
     * @param SessionService $sessionService
     */
    public function __construct(
        Renderer $renderer,
        UserService $userService,
        SessionService $sessionService
    ) {
        $this->renderer = $renderer;
        $this->userService = $userService;
        $this->sessionService = $sessionService;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function index(Request $request): RedirectResponse
    {
        if (!$this->sessionService->isAuthenticated($request->getSession())) {
            return new RedirectResponse('/backoffice/login');
        }

        return new RedirectResponse('/backoffice/sections');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function login(Request $request): Response
    {
        $error = '';
        if ($request->get('name') && $request->get('password')) {
            try {
                $user = $this->userService->authenticate($request->get('name'), $request->get('password'));
                $this->sessionService->setUserId($request->getSession(), $user->getId());

                return new RedirectResponse('/backoffice');
            } catch (AuthenticationFailure $e) {
                $error = 'wrong name or password';
            }
        }

        return $this->renderer->getHtmlResponse(
            '/backoffice/login',
            [
                'error' => $error,
                'name' => $request->get('name')
            ],
            $request->getSession()
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->sessionService->setUserId($request->getSession(), null);

        return new RedirectResponse('/backoffice/login');
    }
}
