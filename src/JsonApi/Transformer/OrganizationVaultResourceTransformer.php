<?php


namespace App\JsonApi\Transformer;


use App\Entity\OrganizationVault;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * OrganizationVault Resource Transformer.
 */
class OrganizationVaultResourceTransformer extends AbstractResource
{
    /**
     * @inheritDoc
     */
    public function getType($vault): string
    {
        return 'vaults';
    }

    /**
     * @inheritDoc
     */
    public function getId($vault): string
    {
        return (string) $vault->getId();
    }

    /**
     * @inheritDoc
     */
    public function getMeta($vault): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getLinks($vault): ?Links
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes($vault): array
    {
        return [
            'name' => function(OrganizationVault $vault) {
                return $vault->getName();
            }
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDefaultIncludedRelationships($vault): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRelationships($vault): array
    {
        return [
            'owner' => function (OrganizationVault $vault) {
                return ToOneRelationship::create()
                    ->setData($vault->getOwner(), new UserResourceTransformer());
            },
        ];
    }
}