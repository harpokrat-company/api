<?php


namespace App\JsonApi\Hydrator;


use Doctrine\DBAL\Types\ConversionException;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ObjectRepository;
use Paknahad\JsonApiBundle\Exception\InvalidRelationshipValueException;
use Paknahad\JsonApiBundle\Hydrator\ValidatorTrait;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Hydrator\Relationship\ToOneRelationship;

trait ResourceHydratorTrait
{
    use ValidatorTrait;

    /**
     * @param ToOneRelationship $relationship
     * @param $relationshipName
     * @param array $resourceValidTypes
     * @param ObjectRepository $repository
     * @param bool $nullable
     * @return object|null
     * @throws InvalidRelationshipValueException
     * @throws \Exception
     */
    protected function getSingleAssociation(ToOneRelationship $relationship, $relationshipName, array $resourceValidTypes, ObjectRepository $repository, bool $nullable = true) {
        $this->validateRelationType($relationship, $resourceValidTypes);
        $association = null;
        $identifier = $relationship->getResourceIdentifier();
        if ($identifier) {
            try {
                $association = $repository->find($identifier->getId());
            } catch (ConversionException $exception) {
                throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()], 'invalid id format');
            }
            if (!$association) {
                throw new InvalidRelationshipValueException($relationshipName, [$identifier->getId()], 'not found');
            }
        }
        if (!$association && !$nullable) {
            throw new BadRequestHttpException($relationshipName . ' cannot be null');
        }
        return $association;
    }

    /**
     * @param ToManyRelationship $relationship
     * @param $relationshipName
     * @param array $resourceValidTypes
     * @param ObjectRepository $repository
     * @return array
     * @throws InvalidRelationshipValueException
     * @throws \Exception
     */
    protected function getCollectionAssociation(ToManyRelationship $relationship, $relationshipName, array $resourceValidTypes, ObjectRepository $repository) {
        $this->validateRelationType($relationship, $resourceValidTypes);
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