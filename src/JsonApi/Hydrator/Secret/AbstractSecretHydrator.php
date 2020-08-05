<?php

namespace App\JsonApi\Hydrator\Secret;

use App\Exception\NotImplementedException;
use App\JsonApi\Hydrator\ResourceHydratorTrait;
use Paknahad\JsonApiBundle\Exception\InvalidAttributeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Secret;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

/**
 * Abstract Secret Hydrator.
 */
abstract class AbstractSecretHydrator extends AbstractHydrator
{
    use ResourceHydratorTrait;

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
        ];
    }

    /**
     * {@inheritdoc}
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
