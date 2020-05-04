<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\Organization;
use App\Entity\OrganizationGroup;
use Doctrine\ORM\Query\Expr;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractOrganizationGroupHydrator extends AbstractHydrator
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
     * @param ToManyRelationship $members
     * @param $relationshipName
     * @return array|mixed
     * @throws InvalidRelationshipValueException
     * @throws \Exception
     */
    protected function getRelationshipMembers(ToManyRelationship $members, $relationshipName) {
        $this->validateRelationType($members, ['users']);

        if (!$members->isEmpty()) {
            $association = $this->objectManager->getRepository('App\Entity\User')
                ->createQueryBuilder('l')
                ->where((new Expr())->in('l.id', $members->getResourceIdentifierIds()))
                ->getQuery()
                ->getResult();

            $this->validateRelationValues($association, $members->getResourceIdentifierIds(), $relationshipName);
        } else {
            $association = [];
        }

        return $association;
    }

    /**
     * @param ToManyRelationship $children
     * @param $relationshipName
     * @return array|mixed
     * @throws InvalidRelationshipValueException
     * @throws \Exception
     */
    protected function getRelationshipChildren(ToManyRelationship $children, $relationshipName) {
        $this->validateRelationType($children, ['groups']);

        if (!$children->isEmpty()) {
            $association = $this->objectManager->getRepository('App\Entity\OrganizationGroup')
                ->createQueryBuilder('l')
                ->where((new Expr())->in('l.id', $children->getResourceIdentifierIds()))
                ->getQuery()
                ->getResult();

            $this->validateRelationValues($association, $children->getResourceIdentifierIds(), $relationshipName);
        } else {
            $association = [];
        }

        return $association;
    }

    /**
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($group): array
    {
        return [
            'organization' => function (OrganizationGroup $group, ToOneRelationship $organization, $data, $relationshipName) {
                $this->validateRelationType($organization, ['organizations']);

                $association = null;
                $identifier = $organization->getResourceIdentifier();
                if ($identifier) {
                    $association = $this->objectManager->getRepository('App\Entity\Organization')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                    $group->setOrganization($association);
                }

            },
            'members' => function (OrganizationGroup $group, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                if ($group->getMembers()->count() > 0) {
                    foreach ($group->getMembers() as $member) {
                        $group->removeMember($member);
                    }
                }

                foreach ($association as $member) {
                    $group->addMember($member);
                }
            },
            'children' => function (OrganizationGroup $group, ToManyRelationship $children, $data, $relationshipName) {
                $association = $this->getRelationshipChildren($children, $relationshipName);

                if ($group->getChildren()->count() > 0) {
                    foreach ($group->getChildren() as $child) {
                        $group->removeChild($child);
                    }
                }

                foreach ($association as $child) {
                    $group->addChild($child);
                }
            }
        ];
    }
}