<?php


namespace App\JsonApi\Document\Log;


use WoohooLabs\Yin\JsonApi\Document\AbstractSingleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class LogRelatedEntityDocument extends AbstractSingleResourceDocument
{
    private $logId;
    private $relationshipName;

    public function __construct($transformer, $logId, string $relationshipName)
    {
        parent::__construct($transformer);
        $this->logId = $logId;
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
            'self' => new Link('/v1/logs/' . $this->logId . '/' . $this->relationshipName),
        ]);
    }
}