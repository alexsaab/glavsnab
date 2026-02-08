<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class ProductTest extends TestCase
{
    public function getValidProduct(): Product
    {
        return (new Product())
            ->setName('Test Product')
            ->setPrice(10.50)
            ->setStatus('active')
            ->setCreatedAt(new \DateTimeImmutable());
    }

    public function testProductIsValid(): void
    {
        $product = $this->getValidProduct();
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $violations = $validator->validate($product);

        $this->assertCount(0, $violations);
    }

    public function testProductNameIsInvalid(): void
    {
        $product = $this->getValidProduct()->setName('');
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $violations = $validator->validate($product);

        $this->assertCount(2, $violations); // Expecting 2 violations for NotBlank and Length(min:3)
        $this->assertEquals('This value should not be blank.', $violations[0]->getMessage());
    }

    public function testProductPriceIsInvalid(): void
    {
        $product = $this->getValidProduct()->setPrice(-10.50);
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $violations = $validator->validate($product);

        $this->assertCount(1, $violations);
        $this->assertEquals('This value should be either positive or zero.', $violations[0]->getMessage());
    }

    public function testProductStatusIsInvalid(): void
    {
        $product = $this->getValidProduct()->setStatus('invalid');
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $violations = $validator->validate($product);

        $this->assertCount(1, $violations);
        $this->assertEquals('The value you selected is not a valid choice.', $violations[0]->getMessage());
    }



    public function testGettersAndSetters(): void
    {
        $product = new Product();
        $dateTime = new \DateTimeImmutable();

        $product->setName('Another Product');
        $product->setPrice(20.00);
        $product->setStatus('inactive');
        $product->setCreatedAt($dateTime);

        $this->assertEquals('Another Product', $product->getName());
        $this->assertEquals(20.00, $product->getPrice());
        $this->assertEquals('inactive', $product->getStatus());
        $this->assertEquals($dateTime, $product->getCreatedAt());
    }
}
