<?php


namespace App\JsonApi\Document\OrganizationGroup;


use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

/**
 * OrganizationGroup Document.
 */
class OrganizationGroupDocument extends AbstractSingleResourceDocument
{
    /**
     * @inheritDoc
     */
    public function getJsonApi(): ?JsonApiObject
    {
        return new JsonApiObject('1.0');
    }

    /**
     * @inheritDoc
     */
    public function getMeta(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getLinks(): ?Links
    {
        return Links::createWithoutBaseUri(
            [
                'self' => new Link('/v1/groups/'.$this->getResourceId()),
            ]
        );
    }
}