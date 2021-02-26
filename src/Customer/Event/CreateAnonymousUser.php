<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Customer\Event;

use OxidEsales\Eshop\Application\Model\User as EshopUserModel;
use Symfony\Component\EventDispatcher\Event;

final class CreateAnonymousUser extends Event
{
    public const NAME = self::class;

    /** @var EshopUserModel */
    private $user;

    /**
     * CreateAnonymousUser constructor.
     */
    public function __construct(EshopUserModel $user)
    {
        $this->user = $user;
    }

    public function getUser(): EshopUserModel
    {
        return $this->user;
    }
}
