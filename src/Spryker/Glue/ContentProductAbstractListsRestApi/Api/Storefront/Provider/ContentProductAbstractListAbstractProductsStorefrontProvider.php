<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\ContentProductAbstractListsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\AbstractProductsStorefrontResource;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\ContentProduct\ContentProductClientInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\ContentProductAbstractListsRestApiConfig;
use Spryker\Glue\ProductsRestApi\Api\Storefront\Mapper\AbstractProductsResourceMapperInterface;
use Spryker\Glue\ProductsRestApi\Api\Storefront\Reader\AbstractProductsAttributesReaderInterface;
use Spryker\Service\Serializer\SerializerServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class ContentProductAbstractListAbstractProductsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string URI_VAR_ID = 'id';

    public function __construct(
        protected ContentProductClientInterface $contentProductClient,
        protected AbstractProductsAttributesReaderInterface $abstractProductsAttributesReader,
        protected AbstractProductsResourceMapperInterface $abstractProductsResourceMapper,
        protected SerializerServiceInterface $serializer,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     *
     * @return array<\Generated\Api\Storefront\AbstractProductsStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $contentKey = $this->resolveContentKey();
        $localeName = $this->getLocale()->getLocaleNameOrFail();

        $contentProductAbstractListTypeTransfer = $this->contentProductClient->executeProductAbstractListTypeByKey(
            $contentKey,
            $localeName,
        );

        if ($contentProductAbstractListTypeTransfer === null) {
            throw new GlueApiException(
                Response::HTTP_NOT_FOUND,
                ContentProductAbstractListsRestApiConfig::RESPONSE_CODE_CONTENT_NOT_FOUND,
                ContentProductAbstractListsRestApiConfig::RESPONSE_DETAILS_CONTENT_NOT_FOUND,
            );
        }

        $productAbstractIds = array_map('intval', $contentProductAbstractListTypeTransfer->getIdProductAbstracts());

        if ($productAbstractIds === []) {
            return [];
        }

        return $this->buildResourcesByAbstractProductIds($productAbstractIds, $localeName);
    }

    /**
     * The list answers the same abstract-product elements the resource is read as everywhere else, so
     * it goes through the reader and mapper that resource owns rather than mapping storage data of
     * its own - which is what left the meta fields, the attribute map and the super attributes null.
     *
     * @param array<int> $productAbstractIds
     *
     * @return array<\Generated\Api\Storefront\AbstractProductsStorefrontResource>
     */
    protected function buildResourcesByAbstractProductIds(array $productAbstractIds, string $localeName): array
    {
        $transfers = $this->abstractProductsAttributesReader->findBulkAbstractProductAttributesByIds(
            $productAbstractIds,
            $localeName,
            $this->getStore()->getNameOrFail(),
        );

        $resources = [];

        foreach ($productAbstractIds as $idProductAbstract) {
            if (!isset($transfers[$idProductAbstract])) {
                continue;
            }

            $resources[] = $this->serializer->denormalize(
                $this->abstractProductsResourceMapper->mapAbstractProductsAttributesTransferToResourceData($transfers[$idProductAbstract]),
                AbstractProductsStorefrontResource::class,
            );
        }

        return $resources;
    }

    protected function resolveContentKey(): string
    {
        if (!$this->hasUriVariable(static::URI_VAR_ID)) {
            $this->throwMissingContentKey();
        }

        $contentKey = (string)$this->getUriVariable(static::URI_VAR_ID);

        if ($contentKey === '') {
            $this->throwMissingContentKey();
        }

        return $contentKey;
    }

    protected function throwMissingContentKey(): never
    {
        throw new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            ContentProductAbstractListsRestApiConfig::RESPONSE_CODE_CONTENT_KEY_IS_MISSING,
            ContentProductAbstractListsRestApiConfig::RESPONSE_DETAILS_CONTENT_KEY_IS_MISSING,
        );
    }
}
