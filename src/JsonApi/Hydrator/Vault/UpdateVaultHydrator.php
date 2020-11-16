<?php

namespace App\JsonApi\Hydrator\Vault;

use App\Entity\Secret;
use App\Entity\Vault;
use App\Exception\InvalidPropertyException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class UpdateVaultHydrator extends AbstractVaultHydrator
{
    protected function getRelationshipHydrator($vault): array
    {
        return [
            'owner' => function (Vault $vault, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'secrets' => function (Vault $vault, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'encryption-key' => function (Vault $vault, ToOneRelationship $relationship, $data, $relationshipName) {
                /** @var ?Secret $encryptionKey */
                $encryptionKey = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['secrets'], $this->objectManager->getRepository('App:Secret'), true
                );
                $vault->setEncryptionKey($encryptionKey);
            },
        ];
    }
}
