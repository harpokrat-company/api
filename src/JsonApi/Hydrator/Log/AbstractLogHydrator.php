<?php

namespace App\JsonApi\Hydrator\Log;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\Log;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

/**
 * Abstract Log Hydrator.
 */
abstract class AbstractLogHydrator extends AbstractHydrator
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
        return ['logs'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($log): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function validateRequest(JsonApiRequestInterface $request): void
    {
        $this->validateFields($this->objectManager->getClassMetadata(Log::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($log, string $id): void
    {
        if ($id && (string) $log->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($log): array
    {
        return [
            'user' => function (Log $log, ToOneRelationship $user, $data, $relationshipName) {
                $this->validateRelationType($user, ['users']);


                $association = null;
                $identifier = $user->getResourceIdentifier();
                if ($identifier) {
                    $association = $this->objectManager->getRepository('App\Entity\User')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                }

                $log->setUser($association);
            },
        ];
    }
}
