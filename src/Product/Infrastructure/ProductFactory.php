<?php

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Infrastructure;

use OxidEsales\EshopCommunity\Application\Model\Article as EshopProductModel;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\Service\Product as ProductService;

final class ProductFactory
{

    /** @var ProductService */
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function createProduct(
        bool $active,
        string $title,
        string $sku,
        string $ean,
        string $varName,
        // optional fields
        ?string $shortDesc,
        ?string $longDesc,
        ?string $manufacturerId,
        ?string $categoryId,
        // ---
        // variant specific fields
        ?string $parentId, // NOTE: MANDATORY if variant. This would be a required field in createVariant.
        ?string $varSelect, // NOTE: MANDATORY if variant. This would be a required field in createVariant.
        ?float $price, // NOTE: MANDATORY if variant. This would be a required field in createVariant.
        ?int $sorting, // NOTE: MANDATORY if variant. This would be a required field in createVariant.
        ?int $stock,
        ?int $stockFlag//,
        // ---
    ): ProductDataType
    {
        /** @var EshopProductModel $model */
        $model = oxNew(EshopProductModel::class);

        $model->assign([
            'oxtype' => 'oxarticle',
            'oxactive' => $active,
            'oxartnum' => $sku,
            'oxtitle' => $title,
            // 'oxean' => $ean,
            'oxdistean' => $ean,
            'oxvarname' => $varName,
            // optional
            'oxshortdesc' => $shortDesc,
            'oxlongdesc' => $longDesc,
            'oxmanufacturerid' => $manufacturerId,
            // ---
            // variant specific
            'oxparentid' => $parentId,
            'oxvarselect' => $varSelect,
            'oxprice' => $price,
            'oxstock' => $stock,
            'oxstockflag' => $stockFlag,
            'oxsort' => $sorting,
        ]);

        $product = new ProductDataType($model);

        if (!empty($categoryId)) {
            // TODO: handle exception
            $this->productService->setCategory($product->getId()->val(), $categoryId);
        }

        return $product;
    }
}
