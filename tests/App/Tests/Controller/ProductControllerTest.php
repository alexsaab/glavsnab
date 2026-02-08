<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use App\Kernel; // Added use statement

class ProductControllerTest extends WebTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?KernelBrowser $client = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->client = null;
        $this->entityManager = null;
        static::$kernel = null;
        static::$booted = false;
    }

    protected static function createKernel(array $options = []): \App\Kernel // Added createKernel method
    {
        return new Kernel('test', true);
    }

    public function testCreateProduct(): void
    {
        $data = [
            'name' => 'Новый товар',
            'price' => 123.45,
            'status' => 'active'
        ];

        $this->client->request(
            Request::METHOD_POST,
            '/api/products',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('Новый товар', $responseData['name']);
    }

    public function testGetProduct(): void
    {
        $product = new Product();
        $product->setName('Для теста');
        $product->setPrice(500.00);
        $product->setStatus('active');
        $product->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->client->request(Request::METHOD_GET, '/api/products/' . $product->getId());

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Для теста', $responseData['name']);
    }

    public function testUpdateProduct(): void
    {
        $product = new Product();
        $product->setName('Старое');
        $product->setPrice(100.00);
        $product->setStatus('active');
        $product->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $data = ['name' => 'Обновлённое', 'price' => 200.00, 'status' => 'active'];

        $this->client->request(
            Request::METHOD_PUT,
            '/api/products/' . $product->getId(),
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Обновлённое', $responseData['name']);
        $this->assertEquals(200.00, $responseData['price']);
    }

    public function testDeleteProduct(): void
    {
        $product = new Product();
        $product->setName('Удалить');
        $product->setPrice(10.00);
        $product->setStatus('active');
        $product->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $productId = $product->getId(); // Store the ID
        $this->assertNotNull($productId); // Assert on the stored ID

        $this->client->request(Request::METHOD_DELETE, '/api/products/' . $productId); // Use stored ID

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->assertNull($this->entityManager->find(Product::class, $productId)); // Use stored ID
    }
}