<?php

namespace App\JsonApi\Document\User;

use WoohooLabs\Yin\JsonApi\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class UserRelatedEntitiesDocument extends AbstractCollectionDocument
{
    private $userId;
    private $relationshipName;

    public function __construct($transformer, $userId, string $relationshipName)
    {
        parent::__construct($transformer);
        $this->userId = $userId;
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
            'self' => new Link('/v1/users/'.$this->userId.'/'.$this->relationshipName),
        ]);
    }
}
