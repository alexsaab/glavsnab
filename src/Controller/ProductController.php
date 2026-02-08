<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name'] ?? '')
            ->setPrice((float)($data['price'] ?? 0.00))
            ->setStatus($data['status'] ?? 'active')
            ->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($product);
        $this->em->flush();

        return $this->json($product, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product);
    }

    #[Route('/{id}', name: 'product_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $product->setName($data['name'] ?? $product->getName())
            ->setPrice((float)($data['price'] ?? $product->getPrice()))
            ->setStatus($data['status'] ?? $product->getStatus());

        $this->em->flush();

        return $this->json($product);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $product = $this->em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($product);
        $this->em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}