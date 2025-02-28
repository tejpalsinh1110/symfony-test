<?php

namespace App\Controller;

use App\Entity\Product;
use App\Enum\ProductCategory;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/products')]
class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly FileUploader $fileUploader,
    ) {
    }

    #[Route('', name: 'app_product_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $sortBy = $request->query->get('sort', 'title');
        $order = $request->query->get('order', 'ASC');
        $category = $request->query->get('category');

        if ($category && !ProductCategory::tryFrom($category)) {
            throw $this->createNotFoundException('Invalid category');
        }

        [$products, $total] = $this->productRepository->findAllWithPagination($page, $limit, $sortBy, $order, $category);

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'currentPage' => $page,
            'totalPages' => ceil($total / $limit),
            'sortBy' => $sortBy,
            'order' => $order,
            'category' => $category,
            'categories' => ProductCategory::getValues(),
            'limit' => $limit,
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $imageFileName = $this->fileUploader->upload($imageFile);
                $product->setImagePath($imageFileName);
            }

            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Product created successfully.');

            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(?Product $product): Response
    {
        if (!$product instanceof Product) {
            throw $this->createNotFoundException('Product not found');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_product_delete', methods: ['POST'])]
    public function delete(string $id): RedirectResponse
    {
        try {
            $uuid = Uuid::fromString($id);
            $product = $this->productRepository->find($uuid);

            if (!$product) {
                throw $this->createNotFoundException('Product not found');
            }

            // Delete the image file if it exists
            if ($product->getImagePath()) {
                $projectDir = $this->getParameter('kernel.project_dir');
                if (!is_string($projectDir)) {
                    throw new \RuntimeException('kernel.project_dir must be a string');
                }
                $imagePath = $projectDir . '/public/media/products/' . $product->getImagePath();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $this->entityManager->remove($product);
            $this->entityManager->flush();

            $this->addFlash('success', 'Product deleted successfully.');
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException('Invalid product ID format');
        }

        return $this->redirectToRoute('app_product_index');
    }
}
