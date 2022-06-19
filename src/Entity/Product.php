<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Index(['flash_sale'], name: 'product_flash_sale')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{

    use Traits\ActiveAwareTrait;
    use Traits\TimestampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['product'])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['product'])]
    private ?string $name;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['product'])]
    private bool $flashSale;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['product'])]
    private ?string $seller;

    #[ORM\ManyToOne(Category::class, inversedBy: 'id')]
    #[Groups(['product'])]
    private ?Category $category;

    #[ORM\Column(type: 'integer')]
    #[Groups(['product'])]
    private ?int $quantity;

    #[ORM\Column(type: 'integer')]
    #[Groups(['product'])]
    private ?int $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function isFlashSale(): ?bool
    {
        return $this->flashSale;
    }

    public function setFlashSale(bool $flashSale): self
    {
        $this->flashSale = $flashSale;

        return $this;
    }

    public function getSeller(): ?string
    {
        return $this->seller;
    }

    public function setSeller(string $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }
}
