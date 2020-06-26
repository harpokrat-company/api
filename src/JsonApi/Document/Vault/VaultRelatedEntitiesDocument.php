<?php


namespace App\JsonApi\Document\Vault;


use WoohooLabs\Yin\JsonApi\Document\AbstractCollectionDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class VaultRelatedEntitiesDocument extends AbstractCollectionDocument
{
    private $vaultId;
    private $relationshipName;

    public function __construct($transformer, $vaultId, string $relationshipName)
    {
        parent::__construct($transformer);
        $this->vaultId = $vaultId;
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
            'self' => new Link('/v1/vaults/' . $this->vaultId . '/' . $this->relationshipName),
        ]);
    }
}