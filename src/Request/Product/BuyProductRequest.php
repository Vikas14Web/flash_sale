<?php

declare(strict_types=1);

namespace App\Request\Product;

use App\Entity\Product;
use App\Request\AbstractValidatedRequest;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

class BuyProductRequest extends AbstractValidatedRequest
{
    #[Assert\NotBlank(allowNull: false, groups: ['PUT'])]
    #[Groups(['PUT'])]
    public int $quantity;

    public function getSubjectClass(): string
    {
        return Product::class;
    }

}
