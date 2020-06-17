<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ContentProductAbstractListsRestApi;

use Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\Client\ContentProductAbstractListsRestApiToCmsStorageClientInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\Client\ContentProductAbstractListsRestApiToContentProductClientInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\Client\ContentProductAbstractListsRestApiToStoreClientInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\RestApiResource\ContentProductAbstractListsRestApiToProductsRestApiResourceInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\Processor\Expander\ContentProductAbstractListsByCmsPageReferenceResourceRelationshipExpander;
use Spryker\Glue\ContentProductAbstractListsRestApi\Processor\Expander\ContentProductAbstractListsByCmsPageReferenceResourceRelationshipExpanderInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\Processor\Reader\ContentProductAbstractListReader;
use Spryker\Glue\ContentProductAbstractListsRestApi\Processor\Reader\ContentProductAbstractListReaderInterface;
use Spryker\Glue\ContentProductAbstractListsRestApi\Processor\RestResponseBuilder\ContentProductAbstractListRestResponseBuilder;
use Spryker\Glue\ContentProductAbstractListsRestApi\Processor\RestResponseBuilder\ContentProductAbstractListRestResponseBuilderInterface;
use Spryker\Glue\Kernel\AbstractFactory;

class ContentProductAbstractListsRestApiFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Glue\ContentProductAbstractListsRestApi\Processor\Reader\ContentProductAbstractListReaderInterface
     */
    public function createContentProductAbstractListReader(): ContentProductAbstractListReaderInterface
    {
        return new ContentProductAbstractListReader(
            $this->getContentProductClient(),
            $this->createContentProductAbstractListRestResponseBuilder(),
            $this->getCmsStorageClient(),
            $this->getStoreClient()
        );
    }

    /**
     * @return \Spryker\Glue\ContentProductAbstractListsRestApi\Processor\RestResponseBuilder\ContentProductAbstractListRestResponseBuilderInterface
     */
    public function createContentProductAbstractListRestResponseBuilder(): ContentProductAbstractListRestResponseBuilderInterface
    {
        return new ContentProductAbstractListRestResponseBuilder(
            $this->getResourceBuilder(),
            $this->getProductRestApiResource()
        );
    }

    /**
     * @return \Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\Client\ContentProductAbstractListsRestApiToContentProductClientInterface
     */
    public function getContentProductClient(): ContentProductAbstractListsRestApiToContentProductClientInterface
    {
        return $this->getProvidedDependency(ContentProductAbstractListsRestApiDependencyProvider::CLIENT_CONTENT_PRODUCT);
    }

    /**
     * @return \Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\Client\ContentProductAbstractListsRestApiToCmsStorageClientInterface
     */
    public function getCmsStorageClient(): ContentProductAbstractListsRestApiToCmsStorageClientInterface
    {
        return $this->getProvidedDependency(ContentProductAbstractListsRestApiDependencyProvider::CLIENT_CMS_STORAGE);
    }

    /**
     * @return \Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\RestApiResource\ContentProductAbstractListsRestApiToProductsRestApiResourceInterface
     */
    public function getProductRestApiResource(): ContentProductAbstractListsRestApiToProductsRestApiResourceInterface
    {
        return $this->getProvidedDependency(ContentProductAbstractListsRestApiDependencyProvider::RESOURCE_PRODUCTS_REST_API);
    }

    /**
     * @return \Spryker\Glue\ContentProductAbstractListsRestApi\Processor\Expander\ContentProductAbstractListsByCmsPageReferenceResourceRelationshipExpanderInterface
     */
    public function createContentProductAbstractListByCmsPageReferenceResourceRelationshipExpander(): ContentProductAbstractListsByCmsPageReferenceResourceRelationshipExpanderInterface
    {
        return new ContentProductAbstractListsByCmsPageReferenceResourceRelationshipExpander(
            $this->createContentProductAbstractListReader()
        );
    }

    /**
     * @return \Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\Client\ContentProductAbstractListsRestApiToStoreClientInterface
     */
    public function getStoreClient(): ContentProductAbstractListsRestApiToStoreClientInterface
    {
        return $this->getProvidedDependency(ContentProductAbstractListsRestApiDependencyProvider::CLIENT_STORE);
    }
}
