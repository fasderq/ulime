<?php
namespace Ulime\API\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Ulime\API\Repository\CategoryRepository;

/**
 * Class ApiController
 * @package Ulime\API\Controller
 */
class ApiController
{
    protected $categoryRepository;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    /**
     * @return JsonResponse
     */
    public function getCategories(): JsonResponse
    {
        return new JsonResponse($this->categoryRepository->getCategories());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getArticles(Request $request): JsonResponse
    {
        return new JsonResponse(
            $this->categoryRepository->getArticlesByCategory(
                $request->get('name')
            )
        );
    }
}
