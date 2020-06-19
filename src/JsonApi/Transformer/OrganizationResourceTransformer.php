<?php


namespace App\JsonApi\Transformer;


use App\Entity\Organization;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Organization Resource Transformer.
 */
class OrganizationResourceTransformer extends AbstractResource
{
    /**
     * @inheritDoc
     */
    public function getType($organization): string
    {
        return 'organizations';
    }

    /**
     * @inheritDoc
     */
    public function getId($organization): string
    {
        return (string) $organization->getId();
    }

    /**
     * @inheritDoc
     */
    public function getMeta($organization): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getLinks($organization): ?Links
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes($organization): array
    {
        return [
            'name' => function(Organization $organization) {
                return $organization->getName();
            }
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDefaultIncludedRelationships($organization): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRelationships($organization): array
    {
        return [
            'members' => function (Organization $organization) {
                return ToManyRelationship::create()
                    ->setData($organization->getMembers(), new UserResourceTransformer())
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/organizations/'. $organization->getId() . '/relationships/members'),
                        'related' => new Link('/v1/organizations/'. $organization->getId() . '/members'),
                    ]));
            },
            'owner' => function (Organization $organization) {
                return ToOneRelationship::create()
                    ->setData($organization->getOwner(), new UserResourceTransformer())
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/organizations/'. $organization->getId() . '/relationships/owner'),
                        'related' => new Link('/v1/organizations/'. $organization->getId() . '/owner'),
                    ]));
            },
        ];
    }
}