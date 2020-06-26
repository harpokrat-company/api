<?php


namespace App\JsonApi\Transformer;


use App\Entity\Vault;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Vault Resource Transformer.
 */
class VaultResourceTransformer extends AbstractResource
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
            'name' => function(Vault $vault) {
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
            'secrets' => function (Vault $user) {
                return ToManyRelationship::create()
                    ->setData($user->getSecrets(), new SecretResourceTransformer())
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/vaults/'. $user->getId() . '/relationships/secrets'),
                        'related' => new Link('/v1/vaults/'. $user->getId() . '/secrets'),
                    ]));
            },
        ];
    }
}