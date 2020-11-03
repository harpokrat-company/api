<?php

namespace App\JsonApi\Document\Organization;

use WoohooLabs\Yin\JsonApi\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class OrganizationRelatedEntityDocument extends AbstractSingleResourceDocument
{
    private $organizationId;
    private $relationshipName;

    public function __construct($transformer, $organizationId, string $relationshipName)
    {
        parent::__construct($transformer);
        $this->organizationId = $organizationId;
        $this->relationshipName = $relationshipName;
    }

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
        return Links::createWithoutBaseUri([
            'self' => new Link('/v1/organizations/'.$this->organizationId.'/'.$this->relationshipName),
        ]);
    }
}
