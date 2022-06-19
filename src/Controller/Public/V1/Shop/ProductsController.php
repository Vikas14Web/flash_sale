<?php

declare(strict_types=1);

namespace App\Controller\Public\V1\Shop;

use App\Controller\BaseController;
use App\Entity\Product;
use App\Request\Product\BuyProductRequest;
use App\Service\ProductService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

/**
 * @OA\Tag(name="Products")
 */
#[Route('/shop/products', name: 'shop_products_')]
class ProductsController extends BaseController
{

    /**
     * Get list of products
     *
     * @OA\Response(
     *     response=200,
     *     description="Get list of products",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="data", type="array",
     *              @OA\Items(ref=@Model(type=Product::class,
     *                  groups={"product_list", "timestamps"})
     *              )
     *         ),
     *         @OA\Property(property="status", type="string")
     *     )
     * )
     * @OA\Response(
     *     response=500,
     *     description="Internal Error occured",
     *      @OA\JsonContent(ref="#/components/schemas/InternalErrorResponse")
     * )
     * @throws \Exception
     */
    #[Route('/flash', name: 'app_products', methods: 'GET')]
    public function index(Request $request, ProductService $productService): Response
    {
        $response = $productService->getFlashSaleProducts();

        return $this->success(
            $response,
            ['product', 'timestamps'],
            Response::HTTP_OK,
            'List of Products'
        );
    }

    /**
     * Buy products.
     *
     * @OA\RequestBody(@Model(type=BuyProductRequest::class, groups={"PUT"}), required=true)
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(type="object",
     *          @OA\Property(property="status", type="string", example="success"),
     *          @OA\Property(property="message", type="string", example=null),
     *          @OA\Property(property="data", type="object", ref=@Model(type=Reason::class, groups={"reason", "is_active", "timestamps"}))
     *     )
     * )
     * @OA\Response(
     *     response=422,
     *     description="Validation Failed",
     *     @OA\JsonContent(ref="#/components/schemas/ValidationFailedResponse")
     * )
     * @OA\Response(
     *     response=500,
     *     description="Internal Error occured",
     *     @OA\JsonContent(
     *         ref="#/components/schemas/InternalErrorResponse"
     *     )
     * )
     */
    #[Route('/buy/{id}', name: 'buy', methods: ['PUT'])]
    public function buy(BuyProductRequest $request, Product $product, ProductService $productService): Response
    {
        if (0 >= ($product->getQuantity() - 1)) {
            return $this->error($product, Response::HTTP_BAD_REQUEST, 'Sorry sold out!!');
        } else {
            $product = $productService->buyProduct($product);

            return $this->success(
                $product,
                ['product', 'is_active', 'timestamps'],
                Response::HTTP_OK,
                'Happy to buy the product!!'
            );
        }
    }
}
