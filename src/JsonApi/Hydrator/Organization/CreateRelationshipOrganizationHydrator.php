<?php


namespace App\JsonApi\Hydrator\Organization;


use App\Entity\Organization;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class CreateRelationshipOrganizationHydrator extends AbstractOrganizationHydrator
{
    protected function getRelationshipHydrator($organization): array
    {
        return [
            'members' => function (Organization $organization, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                foreach ($association as $member) {
                    $organization->addMember($member);
                }
            },
            'owner' => function (Organization $organization, ToOneRelationship $owner, $data, $relationshipName) {
                throw new BadRequestHttpException();
            },
        ];
    }
}