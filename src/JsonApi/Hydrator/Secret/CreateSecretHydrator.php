<?php

namespace App\JsonApi\Hydrator\Secret;

use App\Entity\Secret;
use App\Entity\SecretOwnership\SecretOwnerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

/**
 * Create Secret Hydrator.
 */
class CreateSecretHydrator extends AbstractSecretHydrator
{
    protected function getContext(): string
    {
        return self::CREATION;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($secret): array
    {
        return [
            'owner' => function (Secret $secret, ToOneRelationship $relationship, $data, $relationshipName) {
                $repositoryByType = [
                    'groups' => 'App:OrganizationGroup',
                    'users' => 'App:User',
                    'vaults' => 'App:Vault',
                ];
                $type = $relationship->getResourceIdentifier()->getType();
                if (!key_exists($type, $repositoryByType)) {
                    throw new ValidatorException('Invalid type: '.$relationship->getResourceIdentifier()->getType());
                }
                /** @var SecretOwnerInterface $owner */
                $owner = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['groups', 'users', 'vaults'], $this->objectManager->getRepository($repositoryByType[$type]), false
                );
                $secret->setOwner($owner);
            },
        ];
    }
}
