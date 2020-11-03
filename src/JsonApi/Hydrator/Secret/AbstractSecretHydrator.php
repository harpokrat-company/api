<?php

namespace App\JsonApi\Hydrator\Secret;

use App\Entity\Secret;
use App\Exception\NotImplementedException;
use App\JsonApi\Hydrator\AbstractHydrator;
use App\JsonApi\Hydrator\ResourceHydratorTrait;
use Paknahad\JsonApiBundle\Exception\InvalidAttributeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

/**
 * Abstract Secret Hydrator.
 */
abstract class AbstractSecretHydrator extends AbstractHydrator
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
        return ['secrets'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($secret): array
    {
        return [
            'content' => function (Secret $secret, $attribute, $data, $attributeName) {
                $secret->setContent($attribute);
            },
            'private' => function (Secret $secret, $attribute, $data, $attributeName) {
                $secret->setPrivate($attribute ?? true);
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
        $this->validateFields($this->objectManager->getClassMetadata(Secret::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($secret, string $id): void
    {
        if ($id && (string) $secret->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($secret): array
    {
        return [
            'owner' => function (Secret $secret, ToOneRelationship $relationship, $data, $relationshipName) {
                throw new NotImplementedException();
            },
        ];
    }
}
