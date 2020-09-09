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
    public function getResourceAttributes($vault): array
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
    public function getResourceRelationships($vault): array
    {
        return [
            'secrets' => function (Vault $vault) {
                return ToManyRelationship::create()
                    ->setData($vault->getSecrets(), new SecretResourceTransformer($this->authorizationChecker))
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/vaults/'. $vault->getId() . '/relationships/secrets'),
                        'related' => new Link('/v1/vaults/'. $vault->getId() . '/secrets'),
                    ]));
            },
            'owner' => function (Vault $vault) {
                $owner = $vault->getOwner();
                if ($owner instanceof User)
                    $transformer = new UserResourceTransformer($this->authorizationChecker);
                else if ($owner instanceof OrganizationGroup)
                    $transformer = new OrganizationGroupResourceTransformer($this->authorizationChecker);
                else
                    throw new LogicException();
                return ToOneRelationship::create()
                    ->setData($owner, $transformer)
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/vaults/' . $vault->getId() . '/relationships/owner'),
                        'related' => new Link('/v1/vaults/' . $vault->getId() . '/owner'),
                    ]));
            },
        ];
    }
}