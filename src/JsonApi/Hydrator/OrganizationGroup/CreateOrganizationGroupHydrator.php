<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\Organization;
use App\Entity\OrganizationGroup;
use App\Entity\User;
use App\Exception\NotImplementedException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class CreateOrganizationGroupHydrator extends AbstractOrganizationGroupHydrator
{
    protected function getContext(): string
    {
        return self::CREATION;
    }

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
                /** @var Organization $organization */
                $organization = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['organizations'], $this->objectManager->getRepository('App:Organization'), false
                );
                $group->setOrganization($organization);
            },
            'secrets' => function (OrganizationGroup $group, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'parent' => function (OrganizationGroup $group, ToOneRelationship $relationship, $data, $relationshipName) {
                /** @var OrganizationGroup $parent */
                $parent = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['groups'], $this->objectManager->getRepository('App:OrganizationGroup')
                );
                $group->setParent($parent);
            },
        ];
    }
}