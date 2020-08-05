<?php


namespace App\JsonApi\Document\OrganizationGroup;


use WoohooLabs\Yin\JsonApi\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class OrganizationGroupRelatedEntityDocument extends AbstractSingleResourceDocument
{
    private $groupId;
    private $relationshipName;

    public function __construct($transformer, $groupId, string $relationshipName)
    {
        parent::__construct($transformer);
        $this->groupId = $groupId;
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
            'self' => new Link('/v1/groups/' . $this->groupId . '/' . $this->relationshipName),
        ]);
    }
}