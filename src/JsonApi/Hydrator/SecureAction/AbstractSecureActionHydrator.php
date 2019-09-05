<?php

namespace App\JsonApi\Hydrator\SecureAction;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\SecureAction;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

/**
 * Abstract SecureAction Hydrator.
 */
abstract class AbstractSecureActionHydrator extends AbstractHydrator
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
        return ['secure-actions'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($secureAction): array
    {
        return [
            'validated' => function (SecureAction $secureAction, $attribute, $data, $attributeName) {
                $secureAction->setValidated($attribute);
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function validateRequest(JsonApiRequestInterface $request): void
    {
        // TODO not working for datetime but should not be used anyway on this class. Need todo on future entities
        $this->validateFields($this->objectManager->getClassMetadata(SecureAction::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($secureAction, string $id): void
    {
        if ($id && (string) $secureAction->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($secureAction): array
    {
        return [
        ];
    }
}
