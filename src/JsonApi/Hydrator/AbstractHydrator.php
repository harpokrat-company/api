<?php

namespace App\JsonApi\Hydrator;

use App\Exception\PropertyAccessDeniedException;
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

    const CREATION = 'create';
    const EDITION = 'edit';

    abstract protected function getContext(): string;

    // TODO : maybe find a better way
    /**
     * @throws PropertyAccessDeniedException
     */
    protected function hydrateAttributes($domainObject, array $data)
    {
        if (empty($data['attributes'])) {
            return $domainObject;
        }

        $attributeHydrator = $this->getAttributeHydrator($domainObject);

        foreach ($data['attributes'] as $name => $value) {
            if (!array_key_exists($name, $attributeHydrator)) {
                continue;
            }

            if ($this->checkHydrate && !$this->authorizationChecker->isGranted($this->getContext().'-'.$name, $domainObject)) {
                throw new PropertyAccessDeniedException($name);
            }

            $result = $attributeHydrator[$name](
                $domainObject,
                $value,
                $data,
                $name
            );

            if ($result) {
                $domainObject = $result;
            }
        }

        return $domainObject;
    }

    // TODO : maybe find a better way
    protected function hydrateRelationships($domainObject, array $data, ExceptionFactoryInterface $exceptionFactory)
    {
        if (empty($data['relationships'])) {
            return $domainObject;
        }


        $relationshipHydrator = $this->getRelationshipHydrator($domainObject);

        foreach ($data['relationships'] as $name => $value) {
            if (!array_key_exists($name, $relationshipHydrator)) {
                continue;
            }

            if ($this->checkHydrate && !$this->authorizationChecker->isGranted($this->getContext().'-'.$value, $domainObject)) {
                throw new PropertyAccessDeniedException($name);
            }

            $domainObject = $this->doHydrateRelationship(
                $domainObject,
                $name,
                $relationshipHydrator[$name],
                $exceptionFactory,
                $value,
                $data
            );
        }

        return $domainObject;
    }
}
