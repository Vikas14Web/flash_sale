<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use App\Helper\Filter;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class ProductService extends AbstractEntityService
{
    protected ProductRepository $repository;

    public function __construct(ManagerRegistry $managerRegistry, ProductRepository $productRepository)
    {
        parent::__construct($managerRegistry);
        $this->repository = $productRepository;
    }

    public function getFlashSaleProducts(): array
    {
        return $this->repository->findBy(['flashSale'=>1]);
    }

    public function buyProduct(Product $product): Product
    {
        $product->setQuantity($product->getQuantity()-1);
        $this->save($product);

        // ================== create an order for the logged in user ===========
        // ==================

        return $product;
    }

}
