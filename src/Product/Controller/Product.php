<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Controller;

use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\DataType\Sorting;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;
use TheCodingMachine\GraphQLite\Annotations\Query;
use TheCodingMachine\GraphQLite\Annotations\Mutation;

final class Product
{
    /** @var ProductService */
    private $productService;

    public function __construct(
        ProductService $productService
    )
    {
        $this->productService = $productService;
    }

    /**
     * @Query()
     */
    public function product(string $id): ProductDataType
    {
        return $this->productService->product($id);
    }

    /**
     * @Query()
     */
    public function productBySku(string $sku): ProductDataType
    {
        $id = $this->productService->getIdBySku($sku);
        return $this->productService->product($id);
    }

    /**
     * @Query()
     *
     * @return ProductDataType[]
     */
    public function products(
        ?ProductFilterList $filter = null,
        ?PaginationFilter $pagination = null,
        ?Sorting $sort = null
    ): array
    {
        return $this->productService->products(
            $filter ?? new ProductFilterList(),
            $pagination,
            $sort ?? Sorting::fromUserInput()
        );
    }

    /**
     * @Mutation()
     */
    public function createProduct(ProductDataType $product): ?ProductDataType
    {
        $saved = $this->productService->saveProduct($product);

        if ($saved) {
            return $product;
        }

        return null;
    }

    /**
     * This SHOULD use a "createVariant" factory. But this is blocked on the graphql-base module using gqllite 3...
     *
     * @Mutation()
     */
    public function createVariant(ProductDataType $product): ?ProductDataType
    {
        $saved = $this->productService->saveProduct($product);

        if ($saved) {
            return $product;
        }

        return null;
    }

    /**
     * @Mutation
     * @throws ProductNotFound|InvalidLogin
     */
    public function deleteProduct(string $productId): bool
    {
        try {
            $product = $this->productService->product($productId);

            // TODO handle exception
            return $product->getEshopModel()->delete($product->getId()->val());
        } catch (ProductNotFound $e) {
            throw ProductNotFound::byId($productId);
        } // TODO handle InvalidLogin exception

        return false;
    }

    /**
     * @Mutation
     *
     * @param string $productId
     * @param int $stock
     * @return bool
     * @throws ProductNotFound
     */
    public function updateStock(string $productId, int $stock): bool
    {
        $stockFlag = $stock >= 1 ? 1 : 2;
        try {
            $model = $this->productService->product($productId)->getEshopModel();
            $model->assign([
                "oxstock" => $stock,
                "oxstockflag" => $stockFlag
            ]);
            return $model->save();
        } catch (InvalidLogin $e)
        {
            // todo DO SOMETHING
            // do nothing...
        } catch (ProductNotFound $e) {
            throw ProductNotFound::byId($productId);
        }
    }

    /**
     * @Mutation
     *
     * @throws InvalidLogin
     * @throws ProductNotFound
     */
    public function updateImages(
        string $id,
        // images
        ?string $pic1,
        ?string $pic2,
        ?string $pic3,
        ?string $pic4,
        ?string $pic5,
        ?string $pic6,
        ?string $pic7,
        ?string $pic8,
        ?string $pic9,
        ?string $pic10,
        ?string $pic11,
        ?string $pic12
    ): bool
    {
        try {
            $model = $this->productService->product($id)->getEshopModel();
            $model->assign([
                'oxpic1' => $pic1,
                'oxpic2' => $pic2,
                'oxpic3' => $pic3,
                'oxpic4' => $pic4,
                'oxpic5' => $pic5,
                'oxpic6' => $pic6,
                'oxpic7' => $pic7,
                'oxpic8' => $pic8,
                'oxpic9' => $pic9,
                'oxpic10' => $pic10,
                'oxpic11' => $pic11,
                'oxpic12' => $pic12,
            ]);

            // this nominally returns the ID of the saved article. Dunno why. Actually do, but it's stupid nonetheless.
            // So I cast it to a bool using PHPs ridiculous typecasting.
            return boolval($model->save());
        } catch (ProductNotFound $e) {
            throw ProductNotFound::byId($id);
        }
    }
}
