<?php
namespace Ulime\Backoffice\User\Model;

/**
 * Class User
 * @package Ulime\Backoffice\User\Model
 */
class User
{
    protected $id;
    protected $name;
    protected $password;

    /**
     * User constructor.
     * @param string $id
     * @param string $name
     * @param string $password
     */
    public function __construct(
        string $id,
        string $name,
        string $password
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
