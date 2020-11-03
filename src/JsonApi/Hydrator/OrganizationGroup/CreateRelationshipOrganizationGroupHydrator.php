<?php

namespace App\JsonApi\Hydrator\OrganizationGroup;

use App\Entity\OrganizationGroup;
use App\Entity\User;
use App\Exception\InvalidPropertyException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class CreateRelationshipOrganizationGroupHydrator extends AbstractOrganizationGroupHydrator
{
    protected function getRelationshipHydrator($group): array
    {
        return [
            'children' => function (OrganizationGroup $group, ToManyRelationship $relationship, $data, $relationshipName) {
                /** @var OrganizationGroup[] $members */
                $members = $this->getCollectionAssociation(
                    $relationship, $relationshipName, ['groups'], $this->objectManager->getRepository('App:OrganizationGroup')
                );
                foreach ($members as $child) {
                    $group->addChild($child);
                }
            },
            'members' => function (OrganizationGroup $group, ToManyRelationship $relationship, $data, $relationshipName) {
                /** @var User[] $members */
                $members = $this->getCollectionAssociation(
                    $relationship, $relationshipName, ['users'], $this->objectManager->getRepository('App:User')
                );
                foreach ($members as $member) {
                    $group->addMember($member);
                }
            },
            'organization' => function (OrganizationGroup $group, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new BadRequestHttpException();
            },
            'secrets' => function (OrganizationGroup $group, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'parent' => function (OrganizationGroup $group, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new BadRequestHttpException();
            },
        ];
    }
}
