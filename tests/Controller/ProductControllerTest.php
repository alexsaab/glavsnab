<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Kernel;

class ProductControllerTest extends WebTestCase
{
    protected static function createKernel(array $options = []): Kernel
    {
        return new Kernel('test', true);
    }
    public function testCreateProduct(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'New Product',
                'price' => 99.99,
                'status' => 'active'
            ])
        );

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'name' => 'New Product',
            'price' => 99.99,
            'status' => 'active'
        ]);
        $this->assertArrayHasKey('id', json_decode($client->getResponse()->getContent(), true));
        $this->assertArrayHasKey('createdAt', json_decode($client->getResponse()->getContent(), true));
    }

    public function testCreateProductWithInvalidData(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => '', // Invalid name
                'price' => -10.00, // Invalid price
                'status' => 'invalid' // Invalid status
            ])
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains([
            'errors' => 'Object(App\Entity\Product).name: This value should not be blank.
Object(App\Entity\Product).price: This value should be either positive or zero.
Object(App\Entity\Product).status: The value you selected is not a valid choice.
'
        ]);
    }

    public function testGetProduct(): void
    {
        $client = static::createClient();
        
        // First, create a product to retrieve
        $client->request(
            'POST',
            '/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Product for Get',
                'price' => 123.45,
                'status' => 'active'
            ])
        );
        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $productId = $responseData['id'];

        // Now, retrieve the product
        $client->request('GET', '/products/' . $productId);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $productId,
            'name' => 'Product for Get',
            'price' => 123.45,
            'status' => 'active'
        ]);
    }

    public function testUpdateProduct(): void
    {
        $client = static::createClient();

        // First, create a product to update
        $client->request(
            'POST',
            '/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Product for Update',
                'price' => 100.00,
                'status' => 'active'
            ])
        );
        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $productId = $responseData['id'];

        // Now, update the product
        $client->request(
            'PUT',
            '/products/' . $productId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Updated Product',
                'price' => 150.00,
                'status' => 'inactive'
            ])
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'id' => $productId,
            'name' => 'Updated Product',
            'price' => 150.00,
            'status' => 'inactive'
        ]);
    }

    public function testDeleteProduct(): void
    {
        $client = static::createClient();

        // First, create a product to delete
        $client->request(
            'POST',
            '/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'Product for Delete',
                'price' => 50.00,
                'status' => 'active'
            ])
        );
        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $productId = $responseData['id'];

        // Now, delete the product
        $client->request('DELETE', '/products/' . $productId);

        $this->assertResponseStatusCodeSame(204);

        // Verify it's gone
        $client->request('GET', '/products/' . $productId);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testListProducts(): void
    {
        $client = static::createClient();

        // Ensure there's at least one product
        $client->request(
            'POST',
            '/products',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'name' => 'List Product 1',
                'price' => 1.00,
                'status' => 'active'
            ])
        );
        $this->assertResponseStatusCodeSame(201);

        $client->request('GET', '/products');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJson($client->getResponse()->getContent());
        $products = json_decode($client->getResponse()->getContent(), true);
        $this->assertIsArray($products);
        $this->assertGreaterThanOrEqual(1, count($products));
    }
}
