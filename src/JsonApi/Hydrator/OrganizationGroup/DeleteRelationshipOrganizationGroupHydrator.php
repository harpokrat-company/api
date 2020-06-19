<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\OrganizationGroup;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class DeleteRelationshipOrganizationGroupHydrator extends AbstractOrganizationGroupHydrator
{
    protected function getRelationshipHydrator($group, $clear = true): array
    {
        return [
            'children' => function (OrganizationGroup $group, ToManyRelationship $children, $data, $relationshipName) {
                $association = $this->getRelationshipChildren($children, $relationshipName);

                if (!$group->getChildren()->isEmpty()) {
                    foreach ($association as $member) {
                        $group->removeChild($member);
                    }
                }
            },
            'members' => function (OrganizationGroup $group, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                if (!$group->getMembers()->isEmpty()) {
                    foreach ($association as $member) {
                        $group->removeMember($member);
                    }
                }
            },
            'organization' => function (OrganizationGroup $group, ToOneRelationship $organization, $data, $relationshipName) {
                throw new BadRequestHttpException();
            },
        ];
    }
}