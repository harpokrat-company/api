<?php


namespace App\JsonApi\Hydrator;


use Doctrine\Common\Persistence\ObjectManager;
use Paknahad\JsonApiBundle\Hydrator\AbstractHydrator as BaseHydrator;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use WoohooLabs\Yin\JsonApi\Exception\ExceptionFactoryInterface;

abstract class AbstractHydrator extends BaseHydrator
{
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    /**
     * @var bool
     */
    private $checkHydrate;

    public function __construct(ObjectManager $objectManager, AuthorizationChecker $authorizationChecker, bool $checkHydrate = true)
    {
        parent::__construct($objectManager);
        $this->authorizationChecker = $authorizationChecker;
        $this->checkHydrate = $checkHydrate;
    }

    # TODO : maybe find a better way
    protected function hydrateAttributes($domainObject, array $data)
    {
        if (empty($data["attributes"])) {
            return $domainObject;
        }

        $attributeHydrator = $this->getAttributeHydrator($domainObject);
        foreach ($attributeHydrator as $attribute => $hydrator) {
            if (array_key_exists($attribute, $data["attributes"]) === false) {
                continue;
            }

            if ($this->checkHydrate && !$this->authorizationChecker->isGranted('edit-' . $attribute, $domainObject)) {
                throw new AccessDeniedHttpException();
            }

            $result = $hydrator($domainObject, $data["attributes"][$attribute], $data, $attribute);
            if ($result) {
                $domainObject = $result;
            }
        }

        return $domainObject;
    }

    # TODO : maybe find a better way
    protected function hydrateRelationships($domainObject, array $data, ExceptionFactoryInterface $exceptionFactory) {
        if (empty($data["relationships"])) {
            return $domainObject;
        }

        $relationshipHydrator = $this->getRelationshipHydrator($domainObject);
        foreach ($relationshipHydrator as $relationship => $hydrator) {
            if (isset($data["relationships"][$relationship]) === false) {
                continue;
            }

            if ($this->checkHydrate && !$this->authorizationChecker->isGranted('edit-' . $relationship, $domainObject)) {
                throw new AccessDeniedHttpException();
            }

            $domainObject = $this->doHydrateRelationship(
                $domainObject,
                $relationship,
                $hydrator,
                $exceptionFactory,
                $data["relationships"][$relationship],
                $data
            );
        }

        return $domainObject;
    }
}