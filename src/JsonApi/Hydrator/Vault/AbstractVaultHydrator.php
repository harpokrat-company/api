<?php

namespace App\JsonApi\Hydrator\Vault;

use App\Entity\Vault;
use App\Exception\InvalidPropertyException;
use App\JsonApi\Hydrator\AbstractHydrator;
use App\JsonApi\Hydrator\ResourceHydratorTrait;
use Paknahad\JsonApiBundle\Exception\InvalidAttributeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractVaultHydrator extends AbstractHydrator
{
    use ResourceHydratorTrait;

    protected function getContext(): string
    {
        return self::EDITION;
    }

    /**
     * {@inheritdoc}
     */
    protected function validateClientGeneratedId(
        string $clientGeneratedId,
        JsonApiRequestInterface $request,
        ExceptionFactoryInterface $exceptionFactory
    ): void {
        if (!empty($clientGeneratedId)) {
            throw $exceptionFactory->createClientGeneratedIdNotSupportedException($request, $clientGeneratedId);
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
            'name' => function (Vault $vault, $attribute, $data, $attributeName) {
                $vault->setName($attribute);
            },
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidAttributeException
     */
    protected function validateRequest(JsonApiRequestInterface $request): void
    {
        $this->validateFields($this->objectManager->getClassMetadata(Vault::class), $request);
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
            'owner' => function (Vault $vault, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'secrets' => function (Vault $vault, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
            'encryption-key' => function (Vault $vault, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new InvalidPropertyException($relationshipName);
            },
        ];
    }
}
