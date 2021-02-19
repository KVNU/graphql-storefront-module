<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Shared\Shop;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

/**
 * User model extended
 *
 * @mixin User
 * @eshopExtension
 */
class User extends User_parent
{
    public function loadByAnonymousId(string $userId): bool
    {
        if (!$this->load($userId)) {
            $queryBuilder = ContainerFactory::getInstance()
                ->getContainer()
                ->get(QueryBuilderFactoryInterface::class)
                ->create();

            $queryBuilder->select('oxuser.oxid')
                ->from('oxuser')
                ->where('(oxuser.OEGRAPHQL_ANON_USERID = :userid)')
                ->setParameters([
                    ':userid' => $userId,
                ]);

            $result = $queryBuilder->execute();
            $id     = $result->fetchOne();

            return $this->load($id);
        }

        return true;
    }

    public function setAnonymousUserId(string $userId): bool
    {
        $this->assign(
            [
                'OEGRAPHQL_ANON_USERID' => $userId,
            ]
        );

        return (bool) $this->save();
    }
}
