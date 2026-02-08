<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class ProductTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $product = new Product();
        $product->setName('Тест');
        $product->setPrice(99.99); // Price is float, not string
        $product->setStatus('inactive');
        $product->setCreatedAt(new \DateTimeImmutable());

        $this->assertEquals('Тест', $product->getName());
        $this->assertEquals(99.99, $product->getPrice());
        $this->assertEquals('inactive', $product->getStatus());
        $this->assertNull($product->getId()); // ID is not generated before persistence
        $this->assertInstanceOf(\DateTimeImmutable::class, $product->getCreatedAt());
    }
}