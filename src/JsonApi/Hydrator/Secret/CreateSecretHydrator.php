<?php

namespace App\JsonApi\Hydrator\Secret;

use App\Entity\Organization;
use App\Entity\User;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

/**
 * Create Secret Hydrator.
 */
class CreateSecretHydrator extends AbstractSecretHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($secret): array
    {
        return [
            'owner' => function (Organization $organization, ToOneRelationship $relationship, $data, $relationshipName) {
                /** @var User $owner */
                $owner = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['users'], $this->objectManager->getRepository('App:User')
                );
                $organization->setOwner($owner);
            },
        ];
    }
}
