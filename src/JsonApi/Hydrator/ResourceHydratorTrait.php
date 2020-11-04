<?php

namespace App\JsonApi\Hydrator;

use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ObjectRepository;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use WoohooLabs\Yin\JsonApi\Exception\JsonApiExceptionInterface;
use WoohooLabs\Yin\JsonApi\Exception\RelationshipTypeInappropriate;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

trait ResourceHydratorTrait
{
    use ValidatorTrait;

    /**
     * @throws JsonApiExceptionInterface
     * @throws InvalidRelationshipValueException
     */
    protected function getSingleAssociation(ToOneRelationship $relationship, $relationshipName, array $resourceValidTypes, ObjectRepository $repository, bool $nullable = true)
    {
        if (!$identifier = $relationship->getResourceIdentifier()) {
            return null;
        }
        try {
            $this->validateRelationType($relationship, $resourceValidTypes);
        } catch (\Exception $exception) {
            throw new RelationshipTypeInappropriate($relationshipName, $relationship->getResourceIdentifier()->getType(), join($resourceValidTypes, '/'));
        }
        try {
            $association = $repository->find($identifier->getId());
        } catch (ConversionException $exception) {
            throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()], '');
        }

        if (null === $association) {
            throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()], 'not found');
        }

        return $association;
    }

    /**
     * @throws JsonApiExceptionInterface
     * @throws InvalidRelationshipValueException
     */
    protected function getCollectionAssociation(ToManyRelationship $relationship, $relationshipName, array $resourceValidTypes, ObjectRepository $repository)
    {
        try {
            $this->validateRelationType($relationship, $resourceValidTypes);
        } catch (\Exception $exception) {
            throw new RelationshipTypeInappropriate($relationshipName, join(array_filter($relationship->getResourceIdentifierTypes(), function (string $type) use ($resourceValidTypes) { return !\in_array($type, $resourceValidTypes); }), '/'), join($resourceValidTypes, '/'));
        }
        if (!$relationship->isEmpty()) {
            $association = $repository
                ->createQueryBuilder('l')
                ->where((new Expr())->in('l.id', $relationship->getResourceIdentifierIds()))
                ->getQuery()
                ->getResult();
            $this->validateRelationValues($association, $relationship->getResourceIdentifierIds(), $relationshipName);
        } else {
            $association = [];
        }

        return $association;
    }
}
