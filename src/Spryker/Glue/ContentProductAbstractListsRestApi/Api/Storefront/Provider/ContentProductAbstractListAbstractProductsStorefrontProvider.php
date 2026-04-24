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
use Spryker\Client\ProductStorage\ProductStorageClientInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\ContentProductAbstractListsRestApiConfig;
use Symfony\Component\HttpFoundation\Response;

class ContentProductAbstractListAbstractProductsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string URI_VAR_ID = 'id';

    public function __construct(
        protected ContentProductClientInterface $contentProductClient,
        protected ProductStorageClientInterface $productStorageClient,
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

        $productAbstractIds = $contentProductAbstractListTypeTransfer->getIdProductAbstracts();

        if ($productAbstractIds === []) {
            return [];
        }

        $resources = [];

        foreach ($productAbstractIds as $idProductAbstract) {
            $productData = $this->productStorageClient->findProductAbstractStorageData((int)$idProductAbstract, $localeName);

            if ($productData === null) {
                continue;
            }

            $resources[] = $this->mapToResource($productData);
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

    /**
     * @param array<string, mixed> $productData
     */
    protected function mapToResource(array $productData): AbstractProductsStorefrontResource
    {
        $resource = new AbstractProductsStorefrontResource();
        $resource->sku = isset($productData['sku']) ? (string)$productData['sku'] : null;
        $resource->name = isset($productData['name']) ? (string)$productData['name'] : null;
        $resource->description = isset($productData['description']) ? (string)$productData['description'] : null;
        $resource->attributes = $productData['attributes'] ?? null;

        return $resource;
    }
}
