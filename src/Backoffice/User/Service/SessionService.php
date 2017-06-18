<?php
namespace Ulime\Backoffice\User\Service;


use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ulime\Backoffice\User\Exception\ResponseException;

/**
 * Class SessionService
 * @package Ulime\Backoffice\User\Service
 */
class SessionService
{
    /**
     * @param SessionInterface $session
     * @param int|null $userId
     */
    public function setUserId(SessionInterface $session, ?int $userId = null): void
    {
        $session->set('user_id', $userId);
    }

    /**
     * @param SessionInterface $session
     * @return bool
     */
    public function isAuthenticated(SessionInterface $session): bool
    {
        return null !== $session->get('user_id', null);
    }

    /**
     * @param SessionInterface $session
     * @return mixed
     * @throws ResponseException
     */
    public function requireUserId(SessionInterface $session)
    {
        if ($userId = $session->get('user_id')) {
            return $userId;
        } else {
            throw new ResponseException(
                new RedirectResponse('/backoffice')
            );
        }
    }
}
