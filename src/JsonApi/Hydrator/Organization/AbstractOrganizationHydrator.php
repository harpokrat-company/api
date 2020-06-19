<?php


namespace App\JsonApi\Hydrator\Organization;


use App\Entity\Organization;
use App\Entity\User;
use Doctrine\ORM\Query\Expr;
use Exception;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequestInterface;

abstract class AbstractOrganizationHydrator extends AbstractHydrator
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
     * @throws \Paknahad\JsonApiBundle\Exception\InvalidAttributeException
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
     * @param ToManyRelationship $members
     * @param $relationshipName
     * @return array|mixed
     * @throws InvalidRelationshipValueException
     * @throws Exception
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
     * {@inheritdoc}
     */
    protected function getRelationshipHydrator($organization): array
    {
        return [
            'members' => function (Organization $organization, ToManyRelationship $members, $data, $relationshipName) {
                $association = $this->getRelationshipMembers($members, $relationshipName);

                if ($organization->getMembers()->count() > 0) {
                    foreach ($organization->getMembers() as $member) {
                        $organization->removeMember($member);
                    }
                }

                foreach ($association as $member) {
                    $organization->addMember($member);
                }
            },
            'owner' => function (Organization $organization, ToOneRelationship $owner, $data, $relationshipName) {
                $this->validateRelationType($owner, ['users']);

                $association = null;
                $identifier = $owner->getResourceIdentifier();
                if ($identifier) {
                    /** @var User $association */
                    $association = $this->objectManager->getRepository('App\Entity\User')
                        ->find($identifier->getId());

                    if (is_null($association)) {
                        throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()]);
                    }
                }

                $organization->setOwner($association);
            },
        ];
    }
}