<?php


namespace App\JsonApi\Hydrator\Organization;


use App\Entity\Organization;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;

class DeleteRelationshipOrganizationHydrator extends AbstractOrganizationHydrator
{
    protected function getRelationshipHydrator($organization, $clear = true): array
    {
        return [
            'members' => function (Organization $organization, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                if (!$organization->getMembers()->isEmpty()) {
                    foreach ($association as $member) {
                        $organization->removeMember($member);
                    }
                }
            }
        ];
    }
}