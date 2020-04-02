<?php


namespace App\JsonApi\Document\OrganizationVault;


use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

/**
 * OrganizationVault Document.
 */
class OrganizationVaultDocument extends AbstractSingleResourceDocument
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
                'self' => new Link('/v1/vaults/'.$this->getResourceId()),
            ]
        );
    }
}