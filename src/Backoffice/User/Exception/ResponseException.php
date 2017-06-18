<?php
namespace Ulime\Backoffice\User\Exception;

use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseException
 * @package Ulime\Backoffice\User\Exception
 */
class ResponseException extends \Exception
{
    protected $response;

    /**
     * ResponseException constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
