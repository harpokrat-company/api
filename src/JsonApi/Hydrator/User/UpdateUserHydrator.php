<?php

namespace App\JsonApi\Hydrator\User;

use App\Entity\Secret;
use App\Entity\User;
use App\Exception\InvalidPropertyException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

/**
 * Update User Hydrator.
 */
class UpdateUserHydrator extends AbstractUserHydrator
{
    protected function getRelationshipHydrator($user): array
    {
        return [
            'logs' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'organizations' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'ownedOrganizations' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'secrets' => function (User $user, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'encryption-key' => function (User $user, ToOneRelationship $relationship, $data, $relationshipName) {
                /** @var ?Secret $encryptionKey */
                $encryptionKey = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['secrets'], $this->objectManager->getRepository('App:Secret'), true
                );
                $user->setEncryptionKey($encryptionKey);
            },
        ];
    }
}
