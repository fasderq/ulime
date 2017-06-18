<?php
namespace Ulime\Backoffice\User\Service;

use Ulime\Backoffice\User\Exception\AuthenticationFailure;
use Ulime\Backoffice\User\Model\User;

class UserService
{
    /**
     * @param int $userId
     * @return User
     */
    public function getUser(int $userId): User
    {
        if (1 === $userId) {
            return new User(1, 'admin', 'password');
        }
    }

    /**
     * @param string $name
     * @param string $password
     * @return User
     * @throws AuthenticationFailure
     */
    public function authenticate(string $name, string $password): User
    {
        if ('admin' !== $name || 'password' !== $password) {
            throw new AuthenticationFailure();
        }

        return $this->getUser(1);
    }
}