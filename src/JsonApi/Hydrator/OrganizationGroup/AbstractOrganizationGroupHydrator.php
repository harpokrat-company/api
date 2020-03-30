<?php


namespace App\JsonApi\Hydrator\OrganizationGroup;


use App\Entity\Group;
use App\Entity\OrganizationGroup;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;
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
                }

                $group->setOrganization($association);
            },
        ];
    }
}