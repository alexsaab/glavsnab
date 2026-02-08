<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/products')]
final class ProductController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'app_product_create', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        $product = new Product();
        $product->setName($data['name'] ?? null);
        $product->setPrice($data['price'] ?? null);
        $product->setStatus($data['status'] ?? 'inactive'); // Default to inactive
        $product->setCreatedAt(new \DateTimeImmutable());

        $errors = $this->validator->validate($product);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['errors' => $errorsString], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->json($product, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_product_get', methods: ['GET'])]
    public function getProduct(Product $product): JsonResponse
    {
        return $this->json($product);
    }

    #[Route('/{id}', name: 'app_product_update', methods: ['PUT'])]
    public function updateProduct(Request $request, Product $product): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $this->json(['message' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }
        if (isset($data['status'])) {
            $product->setStatus($data['status']);
        }

        $errors = $this->validator->validate($product);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new JsonResponse(['errors' => $errorsString], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json($product);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['DELETE'])]
    public function deleteProduct(Product $product): JsonResponse
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('', name: 'app_product_list', methods: ['GET'])]
    public function listProducts(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        return $this->json($products);
    }
}
