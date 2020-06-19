<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\Organization;
use App\Entity\OrganizationGroup;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class CreateOrganizationGroupHydrator extends AbstractOrganizationGroupHydrator
{
    protected function getRelationshipHydrator($group): array
    {
        return [
            'children' => function (OrganizationGroup $group, ToManyRelationship $children, $data, $relationshipName) {
                $association = $this->getRelationshipChildren($children, $relationshipName);

                foreach ($association as $child) {
                    $group->addChild($child);
                }
            },
            'members' => function (OrganizationGroup $group, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                foreach ($association as $member) {
                    $group->addMember($member);
                }
            },
            'organization' => function (OrganizationGroup $group, ToOneRelationship $organization, $data, $relationshipName) {
                $this->validateRelationType($organization, ['organizations']);

                $association = null;
                $identifier = $organization->getResourceIdentifier();
                if ($identifier) {
                    /** @var Organization $association */
                    $association = $this->objectManager->getRepository('App\Entity\Organization')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                    $group->setOrganization($association);
                }
            },
        ];
    }
}