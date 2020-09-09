<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\OrganizationGroup;
use App\Entity\User;
use App\Exception\NotImplementedException;
use App\JsonApi\Hydrator\AbstractHydrator;
use App\JsonApi\Hydrator\ResourceHydratorTrait;
use Paknahad\JsonApiBundle\Exception\InvalidAttributeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractOrganizationGroupHydrator extends AbstractHydrator
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
        return ['groups'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttributeHydrator($group): array
    {
        return [
            'name' => function (OrganizationGroup $group, $attribute, $data, $attributeName) {
                $group->setName($attribute);
            },
        ];
    }

    /**
     * {@inheritdoc}
     * @throws InvalidAttributeException
     */
    protected function validateRequest(JsonApiRequestInterface $request): void
    {
        $this->validateFields($this->objectManager->getClassMetadata(OrganizationGroup::class), $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function setId($group, string $id): void
    {
        if ($id && (string) $group->getId() !== $id) {
            throw new NotFoundHttpException('both ids in url & body bust be same');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($group): array
    {
        return [
            'children' => function (OrganizationGroup $group, ToManyRelationship $relationship, $data, $relationshipName) {
                /** @var OrganizationGroup[] $members */
                $members = $this->getCollectionAssociation(
                    $relationship, $relationshipName, ['groups'], $this->objectManager->getRepository('App:OrganizationGroup')
                );
                if (!$group->getChildren()->isEmpty()) {
                    /** @var OrganizationGroup $child */
                    foreach ($group->getChildren() as $child) {
                        $child->setParent(null);
                        $group->removeChild($child);
                    }
                }
                foreach ($members as $child) {
                    $group->addChild($child);
                    $child->setParent($group);
                }
            },
            'members' => function (OrganizationGroup $group, ToManyRelationship $relationship, $data, $relationshipName) {
                /** @var User[] $members */
                $members = $this->getCollectionAssociation(
                    $relationship, $relationshipName, ['users'], $this->objectManager->getRepository('App:User')
                );
                if ($group->getMembers()->count() > 0) {
                    foreach ($group->getMembers() as $member) {
                        $group->removeMember($member);
                    }
                }
                foreach ($members as $member) {
                    $group->addMember($member);
                }
            },
            'organization' => function (OrganizationGroup $group, ToOneRelationship $organization, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'secrets' => function (OrganizationGroup $group, ToManyRelationship $relationship, $data, $relationshipName) {
                throw new NotImplementedException();
            },
            'parent' => function (OrganizationGroup $group, ToOneRelationship $relationship, $data, $relationshipName) {
                /** @var OrganizationGroup $parent */
                $parent = $this->getSingleAssociation(
                    $relationship, $relationshipName, ['users'], $this->objectManager->getRepository('App:User')
                );
                $group->setParent($parent);
            },
        ];
    }
}