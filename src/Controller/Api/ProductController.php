<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    #[Route('/products', name: 'api_products_list', methods: ['GET'])]
    #[OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'limit', in: 'query', schema: new OA\Schema(type: 'integer'))]
    #[OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'order', in: 'query', schema: new OA\Schema(type: 'string'))]
    #[OA\Parameter(name: 'category', in: 'query', schema: new OA\Schema(type: 'string'))]
    #[OA\Response(
        response: 200,
        description: 'Returns a list of products',
    )]
    public function list(Request $request): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = min(50, $request->query->getInt('limit', 10));
        $sortBy = $request->query->get('sort', 'title');
        $order = $request->query->get('order', 'ASC');
        $category = $request->query->get('category');

        [$products, $total] = $this->productRepository->findAllWithPagination($page, $limit, $sortBy, $order, $category);

        $data = [
            'items' => $products,
            'metadata' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'pages' => ceil($total / $limit),
            ],
        ];

        return new JsonResponse(
            $this->serializer->serialize($data, 'json', ['groups' => ['product:read']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/products/{id}', name: 'api_products_show', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a single product',
    )]
    #[OA\Response(
        response: 404,
        description: 'Product not found',
    )]
    public function show(?Product $product): JsonResponse
    {
        if (!$product instanceof Product) {
            return new JsonResponse(
                $this->serializer->serialize(['message' => 'Product not found'], 'json'),
                Response::HTTP_NOT_FOUND,
                [],
                true
            );
        }

        return new JsonResponse(
            $this->serializer->serialize($product, 'json', ['groups' => ['product:read']]),
            Response::HTTP_OK,
            [],
            true
        );
    }
}
