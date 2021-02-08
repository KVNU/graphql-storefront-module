<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\GraphQL\Storefront\Address\Service;

use GraphQL\Error\Error;
use OxidEsales\GraphQL\Base\Framework\GraphQLQueryHandler;
use OxidEsales\GraphQL\Storefront\Address\DataType\DeliveryAddress;
use OxidEsales\GraphQL\Storefront\Country\DataType\Country;
use OxidEsales\GraphQL\Storefront\Country\DataType\State;
use OxidEsales\GraphQL\Storefront\Country\Exception\CountryNotFound;
use OxidEsales\GraphQL\Storefront\Country\Exception\StateNotFound;
use OxidEsales\GraphQL\Storefront\Country\Service\Country as CountryService;
use OxidEsales\GraphQL\Storefront\Country\Service\State as StateService;
use TheCodingMachine\GraphQLite\Annotations\ExtendType;
use TheCodingMachine\GraphQLite\Annotations\Field;

/**
 * @ExtendType(class=DeliveryAddress::class)
 */
final class DeliveryAddressRelations
{
    /** @var CountryService */
    private $countryService;

    /** @var StateService */
    private $stateService;

    public function __construct(
        CountryService $countryService,
        StateService $stateService
    ) {
        $this->countryService = $countryService;
        $this->stateService   = $stateService;
    }

    /**
     * @Field()
     */
    public function country(DeliveryAddress $deliveryAddress): ?Country
    {
        try {
            return $this->countryService->country(
                (string) $deliveryAddress->countryId()
            );
        } catch (CountryNotFound $e) {
            if (strlen((string) $deliveryAddress->countryId()) > 0) {
                GraphQLQueryHandler::addError(new Error($e->getMessage()));
            }

            return null;
        }
    }

    /**
     * @Field()
     */
    public function state(DeliveryAddress $deliveryAddress): ?State
    {
        try {
            return $this->stateService->state(
                (string) $deliveryAddress->stateId()
            );
        } catch (StateNotFound $e) {
            if (strlen((string) $deliveryAddress->stateId()) > 0) {
                GraphQLQueryHandler::addError(new Error($e->getMessage()));
            }

            return null;
        }
    }
}
