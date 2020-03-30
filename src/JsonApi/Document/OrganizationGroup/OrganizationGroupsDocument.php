<?php


namespace App\JsonApi\Document\OrganizationGroup;


use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class OrganizationGroupsDocument extends AbstractCollectionDocument
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
        return Links::createWithoutBaseUri()
            ->setPagination('/v1/groups', $this->domainObject);
    }
}