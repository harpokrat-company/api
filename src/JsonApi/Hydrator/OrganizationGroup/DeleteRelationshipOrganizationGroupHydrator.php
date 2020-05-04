<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\OrganizationGroup;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;

class DeleteRelationshipOrganizationGroupHydrator extends AbstractOrganizationGroupHydrator
{
    protected function getRelationshipHydrator($group, $clear = true): array
    {
        return [
            'members' => function (OrganizationGroup $group, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                if (!$group->getMembers()->isEmpty()) {
                    foreach ($association as $member) {
                        $group->removeMember($member);
                    }
                }
            },
            'children' => function (OrganizationGroup $group, ToManyRelationship $children, $data, $relationshipName) {
                $association = $this->getRelationshipChildren($children, $relationshipName);

                if (!$group->getChildren()->isEmpty()) {
                    foreach ($association as $member) {
                        $group->removeChild($member);
                    }
                }
            }
        ];
    }
}