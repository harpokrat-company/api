<?php


namespace App\JsonApi\Hydrator\User;


use App\Entity\Organization;
use App\Entity\User;
use App\Exception\NotImplementedException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;

class DeleteRelationshipUserHydrator extends AbstractUserHydrator
{
    protected function getRelationshipHydrator($organization): array
    {
        return [
            'logs' => function (User $user, ToManyRelationship $logs, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'organizations' => function (User $user, ToManyRelationship $organizations, $data, $relationshipName) {
                $association = $this->getRelationShipOrganizations($organizations, 'organizations');
                if ($user->getOrganizations()->isEmpty()) {
                    return;
                }
                /** @var Organization $organization */
                foreach ($association as $organization) {
                    $organization->removeMember($user);
                    $user->removeOrganization($organization);
                }
            },
            'ownedOrganizations' => function (User $user, ToManyRelationship $organizations, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'secrets' => function (User $user, ToManyRelationship $organizations, $data, $relationshipName) {
                throw new NotImplementedException();
            },
        ];
    }

}