<?php

namespace App\JsonApi\Hydrator\Secret;

use App\Entity\Secret;
use App\Entity\User;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
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
            'owner' => function (Secret $secret, ToOneRelationship $owner, $data, $relationshipName) {
                $this->validateRelationType($owner, ['users']);


                $association = null;
                $identifier = $owner->getResourceIdentifier();
                if ($identifier) {
                    /** @var User $association */
                    $association = $this->objectManager->getRepository('App\Entity\User')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                }

                $secret->setOwner($association);
            },
        ];
    }
}
