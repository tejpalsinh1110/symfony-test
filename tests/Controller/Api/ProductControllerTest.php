<?php

namespace App\Tests\Controller\Api;

use App\Entity\Product;
use App\Enum\ProductCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;

        // Clear the test database
        $this->entityManager->createQuery('DELETE FROM App\Entity\Product')->execute();
        $this->entityManager->getConnection()->executeStatement('ALTER TABLE product AUTO_INCREMENT = 1');
    }

    public function testGetProducts(): void
    {
        // Create a test product
        $product = new Product();
        $product->setTitle('Test Product')
            ->setDescription('Test Description')
            ->setPriceExclVat(99.99)
            ->setCategory(ProductCategory::ELECTRONICS)
            ->setImagePath('test-image.jpg');

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Make the request
        $this->client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        if (false === $content) {
            $this->fail('Response content is false');
        }

        $this->assertJson($content);

        $data = json_decode($content, true);
        if (!is_array($data)) {
            $this->fail('Failed to decode JSON response');
        }

        $this->assertArrayHasKey('items', $data);
        $this->assertGreaterThan(0, count($data['items']));
    }

    public function testGetProduct(): void
    {
        // Create a test product
        $product = new Product();
        $product->setTitle('Test Product')
            ->setDescription('Test Description')
            ->setPriceExclVat(99.99)
            ->setCategory(ProductCategory::ELECTRONICS)
            ->setImagePath('test-image.jpg');

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Make the request
        $this->client->request('GET', '/api/products/' . $product->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $content = $this->client->getResponse()->getContent();
        if (false === $content) {
            $this->fail('Response content is false');
        }

        $this->assertJson($content);

        $response = json_decode($content, true);
        if (!is_array($response)) {
            $this->fail('Failed to decode JSON response');
        }

        $this->assertEquals('Test Product', $response['title']);
        $this->assertEquals('Test Description', $response['description']);
        $this->assertEquals(99.99, $response['priceExclVat']);
        $this->assertEquals('Electronics', $response['category']);
        $this->assertEquals('test-image.jpg', $response['imagePath']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up the test database
        $this->entityManager->createQuery('DELETE FROM App\Entity\Product')->execute();
        $this->entityManager->close();
        unset($this->entityManager);
    }
}
