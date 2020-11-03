<?php

namespace App\JsonApi\Hydrator\Organization;

use App\Entity\Organization;
use App\Entity\User;
use App\JsonApi\Hydrator\AbstractHydrator;
use App\JsonApi\Hydrator\ResourceHydratorTrait;
use Paknahad\JsonApiBundle\Exception\InvalidAttributeException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractOrganizationHydrator extends AbstractHydrator
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
        return ['organizations'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($organization): array
    {
        return [
            'name' => function (Organization $organization, $attribute, $data, $attributeName) {
                $organization->setName($attribute);
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
        $this->validateFields($this->objectManager->getClassMetadata(Organization::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($organization, string $id): void
    {
        if ($id && (string) $organization->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($organization): array
    {
        return [
            'groups' => function (Organization $organization, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new BadRequestHttpException();
            },
            'members' => function (Organization $organization, ToManyRelationship $relationship, $data, $relationshipName) {
                /** @var User[] $members */
                $members = $this->getCollectionAssociation(
                    $relationship, $relationshipName, ['users'], $this->objectManager->getRepository('App:User')
                );
                if ($organization->getMembers()->count() > 0) {
                    foreach ($organization->getMembers() as $member) {
                        $organization->removeMember($member);
                    }
                }
                foreach ($members as $member) {
                    $organization->addMember($member);
                }
            },
            'owner' => function (Organization $organization, ToOneRelationship $relationship, $data, $relationshipName) {
                /** @var User $owner */
                $owner = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['users'], $this->objectManager->getRepository('App:User')
                );
                $organization->setOwner($owner);
            },
        ];
    }
}
