<?php
namespace Ulime\API\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ulime\API\Repository\ApiRepository;

/**
 * Class ApiController
 * @package Ulime\API\Controller
 */
class ApiController
{
    protected $apiRepository;

    /**
     * ApiController constructor.
     * @param ApiRepository $apiRepository
     */
    public function __construct(ApiRepository $apiRepository)
    {
        $this->apiRepository = $apiRepository;
    }

    /**
     * @return string
     */
    public function getCategories(): string
    {
        return $this->getJsonResponse(
            $this->apiRepository->getCategories()
        )->getContent();
    }

    /**
     * @param Request $request
     * @return string
     */
    public function getArticles(Request $request): string
    {
        return $this->getJsonResponse(
            $this->apiRepository->getArticlesByCategory(
                $request->get('name')
            )
        )->getContent();
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    protected function getJsonResponse(array $data = []): JsonResponse
    {
        $jsonResponse = new JsonResponse();
        $jsonResponse->setCharset('UTF-8');
        $jsonResponse->headers->set('Content-Type', 'application/json');
        $jsonResponse->setStatusCode(Response::HTTP_OK);
        $jsonResponse->setData($data);
        $jsonResponse->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return $jsonResponse;
    }
}
