<?php

namespace App\Tests\Entity;

use App\Entity\Product;
use App\Enum\ProductCategory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
    }

    public function testNewProductHasUuid(): void
    {
        $this->assertInstanceOf(Uuid::class, $this->product->getId());
    }

    public function testProductSettersAndGetters(): void
    {
        $imagePath = 'test-image.jpg';
        $imageFile = $this->createMock(File::class);

        $this->product
            ->setTitle('Test Product')
            ->setDescription('Test Description')
            ->setPriceExclVat(99.99)
            ->setCategory(ProductCategory::ELECTRONICS)
            ->setImagePath($imagePath)
            ->setImageFile($imageFile);

        $this->assertEquals('Test Product', $this->product->getTitle());
        $this->assertEquals('Test Description', $this->product->getDescription());
        $this->assertEquals(99.99, $this->product->getPriceExclVat());
        $this->assertEquals(ProductCategory::ELECTRONICS, $this->product->getCategory());
        $this->assertEquals($imagePath, $this->product->getImagePath());
        $this->assertSame($imageFile, $this->product->getImageFile());
    }

    public function testNullableImageFields(): void
    {
        $this->assertNull($this->product->getImagePath());
        $this->assertNull($this->product->getImageFile());

        $this->product->setImagePath(null);
        $this->product->setImageFile(null);

        $this->assertNull($this->product->getImagePath());
        $this->assertNull($this->product->getImageFile());
    }
}
