<?php

namespace App\JsonApi\Hydrator\Vault;

use App\Entity\User;
use App\Entity\Vault;
use App\Exception\InvalidPropertyException;
use Symfony\Component\Validator\Exception\ValidatorException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

class CreateVaultHydrator extends AbstractVaultHydrator
{
    protected function getContext(): string
    {
        return self::CREATION;
    }

    protected function getRelationshipHydrator($vault): array
    {
        return [
            'owner' => function (Vault $vault, ToOneRelationship $relationship, $data, $relationshipName) {
                $repositoryByType = [
                    'groups' => 'App:OrganizationGroup',
                    'users' => 'App:User',
                ];
                $type = $relationship->getResourceIdentifier()->getType();
                if (!key_exists($type, $repositoryByType)) {
                    throw new ValidatorException('Invalid type: '.$relationship->getResourceIdentifier()->getType());
                }
                /** @var User $owner */
                $owner = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['groups', 'users'], $this->objectManager->getRepository($repositoryByType[$type]), false
                );
                $vault->setOwner($owner);
            },
            'secrets' => function (Vault $vault, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'encryption-key' => function (Vault $vault, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
        ];
    }
}
