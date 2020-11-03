<?php

namespace App\JsonApi\Transformer;

use App\Entity\OrganizationGroup;
use App\Entity\User;
use App\Entity\Vault;
use LogicException;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;

/**
 * Vault Resource Transformer.
 */
class VaultResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($vault): string
    {
        return 'vaults';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($vault): string
    {
        return (string) $vault->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($vault): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($vault): ?Links
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceAttributes($vault): array
    {
        return [
            'name' => function (Vault $vault) {
                return $vault->getName();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($vault): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceRelationships($vault): array
    {
        return [
            'secrets' => function (Vault $vault) {
                return ToManyRelationship::create()
                    ->setData($vault->getSecrets(), new SecretResourceTransformer($this->authorizationChecker))
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/vaults/'.$vault->getId().'/relationships/secrets'),
                        'related' => new Link('/v1/vaults/'.$vault->getId().'/secrets'),
                    ]));
            },
            'owner' => function (Vault $vault) {
                $owner = $vault->getOwner();
                if ($owner instanceof User) {
                    $transformer = new UserResourceTransformer($this->authorizationChecker);
                } elseif ($owner instanceof OrganizationGroup) {
                    $transformer = new OrganizationGroupResourceTransformer($this->authorizationChecker);
                } else {
                    throw new LogicException();
                }

                return ToOneRelationship::create()
                    ->setData($owner, $transformer)
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/vaults/'.$vault->getId().'/relationships/owner'),
                        'related' => new Link('/v1/vaults/'.$vault->getId().'/owner'),
                    ]));
            },
        ];
    }
}
