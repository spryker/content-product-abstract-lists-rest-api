<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\ContentProductAbstractListsRestApi;

use Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\Client\ContentProductAbstractListsRestApiToContentProductClientBridge;
use Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\Client\ContentProductAbstractListsRestApiToStoreClientBridge;
use Spryker\Glue\ContentProductAbstractListsRestApi\Dependency\RestApiResource\ContentProductAbstractListsRestApiToProductsRestApiResourceBridge;
use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

/**
 * @method \Spryker\Glue\ContentProductAbstractListsRestApi\ContentProductAbstractListsRestApiConfig getConfig()
 */
class ContentProductAbstractListsRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_CONTENT_PRODUCT = 'CLIENT_CONTENT_PRODUCT';

    /**
     * @var string
     */
    public const RESOURCE_PRODUCTS_REST_API = 'RESOURCE_PRODUCTS_REST_API';

    /**
     * @var string
     */
    public const CLIENT_STORE = 'CLIENT_STORE';

    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);

        $container = $this->addContentProductClient($container);
        $container = $this->addProductsRestApiResource($container);
        $container = $this->addStoreClient($container);

        return $container;
    }

    protected function addContentProductClient(Container $container): Container
    {
        $container->set(static::CLIENT_CONTENT_PRODUCT, function (Container $container) {
            return new ContentProductAbstractListsRestApiToContentProductClientBridge(
                $container->getLocator()->contentProduct()->client(),
            );
        });

        return $container;
    }

    protected function addProductsRestApiResource(Container $container): Container
    {
        $container->set(static::RESOURCE_PRODUCTS_REST_API, function (Container $container) {
            return new ContentProductAbstractListsRestApiToProductsRestApiResourceBridge(
                $container->getLocator()->productsRestApi()->resource(),
            );
        });

        return $container;
    }

    protected function addStoreClient(Container $container): Container
    {
        $container->set(static::CLIENT_STORE, function (Container $container) {
            return new ContentProductAbstractListsRestApiToStoreClientBridge(
                $container->getLocator()->store()->client(),
            );
        });

        return $container;
    }
}
