<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Product\Service;

use OxidEsales\GraphQL\Base\DataType\PaginationFilter;
use OxidEsales\GraphQL\Base\DataType\Sorting;
use OxidEsales\GraphQL\Base\Exception\InvalidLogin;
use OxidEsales\GraphQL\Base\Exception\NotFound;
use OxidEsales\GraphQL\Base\Service\Authorization;
use OxidEsales\GraphQL\Storefront\Product\DataType\Product as ProductDataType;
use OxidEsales\GraphQL\Storefront\Product\DataType\ProductFilterList;
use OxidEsales\GraphQL\Storefront\Product\Exception\ProductNotFound;
use OxidEsales\GraphQL\Storefront\Shared\Infrastructure\Repository;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

final class Product
{
    /** @var Repository */
    private $repository;

    /** @var Authorization */
    private $authorizationService;

    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    public function __construct(
        Repository $repository,
        Authorization $authorizationService,
        QueryBuilderFactoryInterface $queryBuilderFactory
    )
    {
        $this->repository = $repository;
        $this->authorizationService = $authorizationService;
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /**
     * @throws InvalidLogin
     * @throws InvalidToken
     */
    public function saveProduct(ProductDataType $product): bool
    {
        return $this->repository->saveModel($product->getEshopModel());
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getIdBySku(string $sku): string
    {
        $queryBuilder = $this->queryBuilderFactory->create();

        $queryBuilder
            ->select('oxid')
            ->from('oxarticles')
            ->where('oxartnum = ?')
            ->setParameter(0, $sku)
            ->setMaxResults(1);

        return $queryBuilder->execute()->fetchColumn(0);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function setCategory(string $productId, string $categoryId): bool
    {
        $queryBuilder = $this->queryBuilderFactory->create();

        $queryBuilder
            ->insert('oxobject2category')
            ->values([
                'oxobjectid' => '?',
                'oxcatnid' => '?',
                //'oxpos' => '?' // Sorting
            ])
            ->setValue(0, $productId)
            ->setValue(1, $categoryId);

        $queryBuilder->execute();

        return true;
    }

    /**
     * @throws ProductNotFound
     * @throws InvalidLogin
     */
    public function product(string $id): ProductDataType
    {
        try {
            /** @var ProductDataType $product */
            $product = $this->repository->getById($id, ProductDataType::class);
        } catch (NotFound $e) {
            throw ProductNotFound::byId($id);
        }

        if ($product->isActive()) {
            return $product;
        }

        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_PRODUCT')) {
            return $product;
        }

        throw new InvalidLogin('Unauthorized');
    }

    /**
     * @return ProductDataType[]
     */
    public function products(
        ProductFilterList $filter,
        ?PaginationFilter $pagination,
        Sorting $sort
    ): array
    {
        // In case user has VIEW_INACTIVE_PRODUCT permissions
        // return all products including inactive ones
        if ($this->authorizationService->isAllowed('VIEW_INACTIVE_PRODUCT')) {
            $filter = $filter->withActiveFilter(null);
        }

        return $this->repository->getList(
            ProductDataType::class,
            $filter,
            $pagination ?? new PaginationFilter(),
            $sort
        );
    }
}
