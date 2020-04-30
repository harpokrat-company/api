<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\OrganizationGroup;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;

class CreateRelationshipOrganizationGroupHydrator extends AbstractOrganizationGroupHydrator
{
    protected function getRelationshipHydrator($group, $clear=null): array
    {
        return [
            'members' => function (OrganizationGroup $group, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                foreach ($association as $member) {
                    $group->addMember($member);
                }
            }
        ];
    }
}