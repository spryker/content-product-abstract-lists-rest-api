<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\ContentProductAbstractListsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\ContentProductAbstractListsStorefrontResource;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Client\ContentProduct\ContentProductClientInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\ContentProductAbstractListsRestApiConfig;
use Symfony\Component\HttpFoundation\Response;

class ContentProductAbstractListsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string URI_VAR_ID = 'id';

    public function __construct(
        protected ContentProductClientInterface $contentProductClient,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function provideItem(): ?object
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

        $idProductAbstracts = $contentProductAbstractListTypeTransfer->getIdProductAbstracts();

        $resource = new ContentProductAbstractListsStorefrontResource();
        $resource->id = $contentKey;
        $resource->idProductAbstracts = $idProductAbstracts !== [] ? array_values($idProductAbstracts) : [];

        return $resource;
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     *
     * @return never
     */
    protected function provideCollection(): array
    {
        throw new GlueApiException(
            Response::HTTP_BAD_REQUEST,
            ContentProductAbstractListsRestApiConfig::RESPONSE_CODE_CONTENT_KEY_IS_MISSING,
            ContentProductAbstractListsRestApiConfig::RESPONSE_DETAILS_CONTENT_KEY_IS_MISSING,
        );
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
