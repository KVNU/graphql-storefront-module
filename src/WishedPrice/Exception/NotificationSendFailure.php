<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\WishedPrice\Exception;

use OxidEsales\GraphQL\Base\Exception\Error;
use OxidEsales\GraphQL\Base\Exception\ErrorCategories;
use OxidEsales\GraphQL\Base\Exception\HttpErrorInterface;

final class NotificationSendFailure extends Error implements HttpErrorInterface
{
    public function getCategory(): string
    {
        return ErrorCategories::REQUESTERROR;
    }

    public function getHttpStatus(): int
    {
        return 500;
    }

    public static function create(string $message): self
    {
        return new self(sprintf('Failed to send notification: %s', $message));
    }
}
