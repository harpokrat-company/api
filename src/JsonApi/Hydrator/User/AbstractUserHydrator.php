<?php

namespace App\JsonApi\Hydrator\User;

use App\Entity\User;
use App\Exception\InvalidPropertyException;
use App\JsonApi\Hydrator\AbstractHydrator;
use App\JsonApi\Hydrator\ResourceHydratorTrait;
use Paknahad\JsonApiBundle\Exception\InvalidAttributeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

/**
 * Abstract User Hydrator.
 */
abstract class AbstractUserHydrator extends AbstractHydrator
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
        return ['users'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($user): array
    {
        return [
            'email' => function (User $user, $attribute, $data, $attributeName) {
                $user->setEmail($attribute);
            },
            'roles' => function (User $user, $attribute, $data, $attributeName) {
                $user->setRoles($attribute);
            },
            'password' => function (User $user, $attribute, $data, $attributeName) {
                $user->setPassword($attribute);
            },
            'firstName' => function (User $user, $attribute, $data, $attributeName) {
                $user->setFirstName($attribute);
            },
            'lastName' => function (User $user, $attribute, $data, $attributeName) {
                $user->setLastName($attribute);
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
        $this->validateFields($this->objectManager->getClassMetadata(User::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($user, string $id): void
    {
        if ($id && (string) $user->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
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
                throw new InvalidPropertyException($relationshipName);
            },
        ];
    }
}
