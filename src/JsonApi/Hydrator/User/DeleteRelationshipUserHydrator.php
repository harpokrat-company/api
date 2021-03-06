<?php

namespace App\JsonApi\Hydrator\User;

use App\Entity\Organization;
use App\Entity\User;
use App\Exception\InvalidPropertyException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class DeleteRelationshipUserHydrator extends AbstractUserHydrator
{
    protected function getRelationshipHydrator($organization): array
    {
        return [
            'logs' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
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
                throw new InvalidPropertyException($relationshipName);
            },
            'secrets' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'encryption-key' => function (User $user, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
        ];
    }
}
