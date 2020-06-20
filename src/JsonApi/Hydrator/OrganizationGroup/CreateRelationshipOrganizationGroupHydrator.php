<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\OrganizationGroup;
use App\Exception\NotImplementedException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class CreateRelationshipOrganizationGroupHydrator extends AbstractOrganizationGroupHydrator
{
    protected function getRelationshipHydrator($group): array
    {
        return [
            'children' => function (OrganizationGroup $group, ToManyRelationship $children, $data, $relationshipName) {
                $association = $this->getRelationshipChildren($children, $relationshipName);

                foreach ($association as $child) {
                    $group->addChild($child);
                    $child->setParent($group);
                }
            },
            'members' => function (OrganizationGroup $group, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                foreach ($association as $member) {
                    $group->addMember($member);
                }
            },
            'organization' => function (OrganizationGroup $group, ToOneRelationship $organization, $data, $relationshipName) {
                throw new BadRequestHttpException();
            },
            'parent' => function (OrganizationGroup $group, ToOneRelationship $children, $data, $relationshipName) {
                throw new BadRequestHttpException();
            },
        ];
    }
}