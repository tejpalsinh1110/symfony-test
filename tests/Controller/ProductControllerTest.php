<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Enum\ProductCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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

        // Ensure upload directory exists and is writable
        $uploadDir = __DIR__ . '/../../public/media/products';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    }

    public function testIndex(): void
    {
        $this->client->request('GET', '/products');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Products');
    }

    public function testNewProductWithoutImage(): void
    {
        $this->client->request('GET', '/products/new');

        $form = $this->client->getCrawler()->filter('form')->form();

        $form['product[title]'] = 'Test Product';
        $form['product[description]'] = 'Test Description';
        $form['product[priceExclVat]'] = '2588.99';
        $form['product[category]'] = (string) array_search(ProductCategory::FASHION->value, array_map(fn ($case) => $case->value, ProductCategory::cases()));
        $form['product[imageFile]'] = '';

        $this->client->submit($form);

        $this->assertResponseRedirects('/products');
        $this->client->followRedirect();

        $product = $this->entityManager->getRepository(Product::class)->findOneBy(['title' => 'Test Product']);
        $this->assertNotNull($product);
        $this->assertEquals('Test Product', $product->getTitle());
        $this->assertEquals('Test Description', $product->getDescription());
        $this->assertEquals('2588.99', $product->getPriceExclVat());
        $this->assertEquals(ProductCategory::FASHION, $product->getCategory());
    }

    public function testNewProductWithImage(): void
    {
        $crawler = $this->client->request('GET', '/products/new');
        $this->assertResponseIsSuccessful();

        // Create a test image file
        $imagePath = sys_get_temp_dir() . '/test_image.png';
        // Create a 1x1 black PNG image
        $image = imagecreatetruecolor(1, 1);
        imagepng($image, $imagePath);
        imagedestroy($image);

        $imageFile = new UploadedFile(
            $imagePath,
            'test_image.png',
            'image/png',
            null,
            true // Set test mode to true
        );

        // Submit the form with the image
        $form = $crawler->selectButton('Create Product')->form([
            'product[title]' => 'Product With Image',
            'product[description]' => 'Test Description',
            'product[priceExclVat]' => 686.65,
            'product[category]' => array_search(ProductCategory::FASHION->value, ProductCategory::getValues(), true),
            'product[imageFile]' => $imageFile,
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects();

        // Get the created product from database
        $product = $this->entityManager->getRepository(Product::class)
            ->findOneBy(['title' => 'Product With Image']);

        $this->assertNotNull($product);
        $this->assertNotNull($product->getImagePath());

        // Clean up the uploaded file
        $uploadedImagePath = __DIR__ . '/../../public/media/products/' . $product->getImagePath();
        if (file_exists($uploadedImagePath)) {
            unlink($uploadedImagePath);
        }

        // Clean up the temp file
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $this->client->followRedirect();
    }

    public function testNewProductValidation(): void
    {
        $crawler = $this->client->request('GET', '/products/new');
        $this->assertResponseIsSuccessful();

        // Create raw form data to simulate invalid submission
        $formData = [
            'product' => [
                'title' => '',
                'description' => '',
                'priceExclVat' => '-10',
                'category' => ProductCategory::FASHION->value,
            ],
        ];

        // Submit the form with raw data
        $crawler = $this->client->request('POST', '/products/new', $formData);

        // Assert that we get a 422 Unprocessable Content status
        $this->assertResponseStatusCodeSame(422);

        // Assert validation errors are displayed for specific fields
        $errors = $crawler->filter('.invalid-feedback');
        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[] = trim($error->textContent);
        }

        // Assert that all expected error messages are present
        $this->assertContains('Please enter a title', $errorMessages);
        $this->assertContains('Please enter a description', $errorMessages);
        $this->assertContains('Price must be greater than zero', $errorMessages);
    }

    public function testDelete(): void
    {
        // Create a test product
        $product = new Product();
        $product->setTitle('Test Product')
            ->setDescription('Test Description')
            ->setPriceExclVat(46.35)
            ->setCategory(ProductCategory::FASHION);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $productId = $product->getId();

        // Delete the product
        $this->client->request('POST', '/products/' . $productId . '/delete');
        $this->assertResponseRedirects('/products');

        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        // Verify the product is deleted
        $deletedProduct = $this->entityManager->find(Product::class, $productId);
        $this->assertNull($deletedProduct);
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
