<?php

namespace App\JsonApi\Hydrator\User;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\User;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use Doctrine\ORM\Query\Expr;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

/**
 * Abstract User Hydrator.
 */
abstract class AbstractUserHydrator extends AbstractHydrator
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
            'secrets' => function (User $user, ToManyRelationship $secrets, $data, $relationshipName) {
                $this->validateRelationType($secrets, ['secrets']);

                if (count($secrets->getResourceIdentifierIds()) > 0) {
                    $association = $this->objectManager->getRepository('App\Entity\Secret')
                        ->createQueryBuilder('s')
                        ->where((new Expr())->in('s.id', $secrets->getResourceIdentifierIds()))
                        ->getQuery()
                        ->getResult();

                    $this->validateRelationValues($association, $secrets->getResourceIdentifierIds(), $relationshipName);
                } else {
                    $association = [];
                }

                if ($user->getSecrets()->count() > 0) {
                    foreach ($user->getSecrets() as $secret) {
                        $user->removeSecret($secret);
                    }
                }

                foreach ($association as $secret) {
                    $user->addSecret($secret);
                }
            },
            'logs' => function (User $user, ToManyRelationship $logs, $data, $relationshipName) {
                $this->validateRelationType($logs, ['logs']);

                if (count($logs->getResourceIdentifierIds()) > 0) {
                    $association = $this->objectManager->getRepository('App\Entity\Log')
                        ->createQueryBuilder('l')
                        ->where((new Expr())->in('l.id', $logs->getResourceIdentifierIds()))
                        ->getQuery()
                        ->getResult();

                    $this->validateRelationValues($association, $logs->getResourceIdentifierIds(), $relationshipName);
                } else {
                    $association = [];
                }

                if ($user->getLogs()->count() > 0) {
                    foreach ($user->getLogs() as $log) {
                        $user->removeLog($log);
                    }
                }

                foreach ($association as $log) {
                    $user->addLog($log);
                }
            },
        ];
    }
}
