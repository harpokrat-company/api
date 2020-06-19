<?php

namespace App\JsonApi\Hydrator\User;

use App\Entity\Organization;
use App\Exception\NotImplementedException;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Entity\User;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
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
     * @throws \Paknahad\JsonApiBundle\Exception\InvalidAttributeException
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
            'logs' => function (User $user, ToManyRelationship $logs, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'organizations' => function (User $user, ToManyRelationship $organizations, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'ownedOrganizations' => function (User $user, ToManyRelationship $organizations, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'secrets' => function (User $user, ToManyRelationship $organizations, $data, $relationshipName) {
                throw new NotImplementedException();
            },
        ];
    }

    /**
     * @param ToManyRelationship $organizations
     * @param $relationshipName
     * @return array
     * @throws Exception
     */
    protected function getRelationShipOrganizations(ToManyRelationship $organizations, $relationshipName) {
        $this->validateRelationType($organizations, ['organizations']);

        if (count($organizations->getResourceIdentifierIds()) > 0) {
            $association = $this->objectManager->getRepository('App:Organization')
                ->createQueryBuilder('l')
                ->where((new Expr())->in('l.id', $organizations->getResourceIdentifierIds()))
                ->getQuery()
                ->getResult();

            $this->validateRelationValues($association, $organizations->getResourceIdentifierIds(), $relationshipName);
        } else {
            $association = [];
        }

        return $association;
    }
}
