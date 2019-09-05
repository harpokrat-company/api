<?php

namespace App\JsonApi\Document\SecureAction;

use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

/**
 * SecureAction Document.
 */
class SecureActionDocument extends AbstractSingleResourceDocument
{
    /**
     * {@inheritdoc}
     */
    public function getJsonApi(): JsonApiObject
    {
        return new JsonApiObject('1.0');
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks(): Links
    {
        return Links::createWithoutBaseUri(
            [
                'self' => new Link('/v1/secure-actions/'.$this->getResourceId()),
            ]
        );
    }
}
