<?php


namespace App\JsonApi\Transformer;


use App\Entity\OrganizationGroup;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * OrganizationGroup Resource Transformer.
 */
class OrganizationGroupResourceTransformer extends AbstractResource
{
    /**
     * @inheritDoc
     */
    public function getType($group): string
    {
        return 'groups';
    }

    /**
     * @inheritDoc
     */
    public function getId($group): string
    {
        return (string) $group->getId();
    }

    /**
     * @inheritDoc
     */
    public function getMeta($group): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getLinks($group): ?Links
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getAttributes($group): array
    {
        return [
            'name' => function(OrganizationGroup $group) {
                return $group->getName();
            }
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDefaultIncludedRelationships($group): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getRelationships($group): array
    {
        return [
            'children' => function (OrganizationGroup $group) {
                return ToManyRelationship::create()
                    ->setData($group->getChildren(), new OrganizationGroupResourceTransformer())
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/groups/' . $group->getId() . '/relationships/children'),
                        'related' => new Link('/v1/groups/' . $group->getId() . '/children'),
                    ]));
            },
            'members' => function (OrganizationGroup $group) {
                return ToManyRelationship::create()
                    ->setData($group->getMembers(), new UserResourceTransformer())
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/groups/'. $group->getId() . '/relationships/members'),
                        'related' => new Link('/v1/groups/'. $group->getId() . '/members'),
                    ]));
            },
            'organization' => function (OrganizationGroup $group) {
                return ToOneRelationship::create()
                    ->setData($group->getOrganization(), new OrganizationResourceTransformer())
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/groups/'. $group->getId() . '/relationships/organization'),
                        'related' => new Link('/v1/groups/'. $group->getId() . '/organization'),
                    ]));
            },
            'parent' => function (OrganizationGroup $group) {
                return ToOneRelationship::create()
                    ->setData($group->getParent(), new OrganizationGroupResourceTransformer())
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/groups/' . $group->getId() . '/relationships/parent'),
                        'related' => new Link('/v1/groups/' . $group->getId() . '/parent'),
                    ]));
            },
        ];
    }
}