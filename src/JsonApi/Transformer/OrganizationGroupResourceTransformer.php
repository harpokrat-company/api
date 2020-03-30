<?php


namespace App\JsonApi\Transformer;


use App\Entity\OrganizationGroup;
use WoohooLabs\Yin\JsonApi\Schema\Links;
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
            'organization' => function (OrganizationGroup $group) {
                return ToOneRelationship::create()
                    ->setData($group->getOrganization(), new OrganizationResourceTransformer());
            },
        ];
    }
}