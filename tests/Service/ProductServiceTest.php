<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{

    public function testGetFlashSaleProducts(){
        $product = new Product();
        $this->repository->method('findBy')->willReturn([$product]);
        $productList = $this->productService->getFlashSaleProducts();
        $this->assertSame([$product], $productList);
    }

    public function testBuyProduct()
    {
        $product = new Product();
        $product->setQuantity(10);
        $this->productService->buyProduct($product);
        $this->assertSame($product, $product);
    }
    protected function setUp(): void
    {
        parent::setUp();

        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->managerRegistry
            ->method('getManager')
            ->willReturn($this->manager = $this->createMock(EntityManagerInterface::class));
        $this->repository = $this->createMock(ProductRepository::class);

        $this->productService = new ProductService(
            $this->managerRegistry,
            $this->repository
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->productService = null;
        $this->managerRegistry = null;
        $this->repository = null;
    }


}
