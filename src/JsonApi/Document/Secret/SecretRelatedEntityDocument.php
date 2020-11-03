<?php

namespace App\JsonApi\Document\Secret;

use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class SecretRelatedEntityDocument extends AbstractSingleResourceDocument
{
    private $secretId;
    private $relationshipName;

    public function __construct($transformer, $secretId, string $relationshipName)
    {
        parent::__construct($transformer);
        $this->secretId = $secretId;
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
            'self' => new Link('/v1/secrets/'.$this->secretId.'/'.$this->relationshipName),
        ]);
    }
}
