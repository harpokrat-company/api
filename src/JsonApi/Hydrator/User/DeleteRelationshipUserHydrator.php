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
            'logs' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'organizations' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                /** @var Organization[] $organizations */
                $organizations = $this->getCollectionAssociation(
                    $relationship, $relationshipName, ['organizations'], $this->objectManager->getRepository('App:Organization')
                );
                foreach ($organizations as $organization) {
                    $organization->removeMember($user);
                    $user->removeOrganization($organization);
                }
            },
            'ownedOrganizations' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'secrets' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new NotImplementedException();
            },
        ];
    }

}