<?php


namespace App\JsonApi\Hydrator\OrganizationVault;


use App\Entity\OrganizationVault;
use App\Entity\Secret;
use App\Entity\User;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractOrganizationVaultHydrator extends AbstractHydrator
{
    use ValidatorTrait;

    /**
     * {@inheritdoc}
     */
    protected function validateClientGeneratedId(
        string $clientGeneratedId,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory
    ): void {
        if (!empty($clientGeneratedId)) {
            throw $exceptionFactory->createClientGeneratedIdNotSupportedException(
                $request,
                $clientGeneratedId
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function generateId(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    protected function getAcceptedTypes(): array
    {
        return ['vaults'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($vault): array
    {
        return [
            'name' => function (OrganizationVault $vault, $attribute, $data, $attributeName) {
                $vault->setName($attribute);
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validateRequest(JsonApiRequestInterface $request): void
    {
        $this->validateFields($this->objectManager->getClassMetadata(OrganizationVault::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($vault, string $id): void
    {
        if ($id && (string) $vault->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($vault): array
    {
        return [
            'owner' => function (OrganizationVault $vault, ToOneRelationship $owner, $data, $relationshipName) {
                $this->validateRelationType($owner, ['users']);

                $association = null;
                $identifier = $owner->getResourceIdentifier();
                if ($identifier) {
                    $association = $this->objectManager->getRepository('App\Entity\User')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                }

                $vault->setOwner($association);
            },
        ];
    }
}