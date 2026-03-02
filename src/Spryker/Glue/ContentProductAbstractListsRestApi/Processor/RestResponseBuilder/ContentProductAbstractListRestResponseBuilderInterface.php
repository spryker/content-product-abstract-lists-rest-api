<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ContentProductAbstractListsRestApi\Processor\RestResponseBuilder;

use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface ContentProductAbstractListRestResponseBuilderInterface
{
    public function createContentItemIdNotSpecifiedErrorResponse(): RestResponseInterface;

    public function createContentItemtNotFoundErrorResponse(): RestResponseInterface;

    public function createContentTypeInvalidErrorResponse(): RestResponseInterface;

    /**
     * @param array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface> $abstractProductResources
     *
     * @return \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface
     */
    public function createContentProductAbstractListProductsRestResponse(array $abstractProductResources): RestResponseInterface;

    /**
     * @param array<string, \Generated\Shared\Transfer\ContentProductAbstractListTypeTransfer> $contentProductAbstractListTypeTransfers
     *
     * @return array<string, \Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface>
     */
    public function createContentProductAbstractListsRestResources(array $contentProductAbstractListTypeTransfers): array;

    public function createContentProductAbstractListRestResponse(RestResourceInterface $contentProductAbstractListRestResource): RestResponseInterface;
}
