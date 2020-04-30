<?php


namespace App\JsonApi\Hydrator\Organization;


use App\Entity\Organization;
use Doctrine\ORM\Query\Expr;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;

class CreateRelationshipOrganizationHydrator extends AbstractOrganizationHydrator
{
    protected function getRelationshipHydrator($organization, $clear=null): array
    {
        return [
            'members' => function (Organization $organization, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                foreach ($association as $member) {
                    $organization->addMember($member);
                }
            }
        ];
    }
}