<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\ContentProductAbstractListsRestApi\Api\Storefront\Relationship;

use Generated\Api\Storefront\AbstractProductsStorefrontResource;
use Spryker\ApiPlatform\Relationship\AbstractRelationshipResolver;
use Spryker\Client\ProductStorage\ProductStorageClientInterface;

class ContentProductAbstractListAbstractProductsRelationshipResolver extends AbstractRelationshipResolver
{
    public function __construct(
        protected ProductStorageClientInterface $productStorageClient,
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\AbstractProductsStorefrontResource>
     */
    protected function resolveRelationship(): array
    {
        $productAbstractIds = $this->extractProductAbstractIds();

        if ($productAbstractIds === []) {
            return [];
        }

        $localeName = $this->hasLocale() ? $this->getLocale()->getLocaleNameOrFail() : '';
        $storeName = $this->hasStore() ? $this->getStore()->getNameOrFail() : '';

        $productAbstractStorageData = $this->productStorageClient
            ->getBulkProductAbstractStorageDataByProductAbstractIdsForLocaleNameAndStore(
                $productAbstractIds,
                $localeName,
                $storeName,
            );

        $resources = [];

        foreach ($productAbstractStorageData as $productData) {
            $resources[] = $this->mapToResource($productData);
        }

        return $resources;
    }

    /**
     * @return array<int>
     */
    protected function extractProductAbstractIds(): array
    {
        $ids = [];

        foreach ($this->getParentResources() as $parent) {
            $idProductAbstracts = $parent->idProductAbstracts ?? [];

            if (!is_array($idProductAbstracts)) {
                continue;
            }

            foreach ($idProductAbstracts as $id) {
                if (!is_int($id)) {
                    continue;
                }

                $ids[$id] = $id;
            }
        }

        return array_values($ids);
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
