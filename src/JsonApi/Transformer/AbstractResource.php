<?php

namespace App\JsonApi\Transformer;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource as BaseResource;

/**
 * Class AbstractResource.
 */
abstract class AbstractResource extends BaseResource
{
    /**
     * @var AuthorizationChecker
     */
    protected $authorizationChecker;

    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    private function filter(array $relationships, $domainObject)
    {
        return array_filter($relationships, function ($relationName) use ($domainObject) {
            return $this->authorizationChecker->isGranted('view-'.$relationName, $domainObject);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function getAttributes($domainObject): array
    {
        $attributes = $this->getResourceAttributes($domainObject);

        return $this->filter($attributes, $domainObject);
    }

    abstract public function getResourceAttributes($domainObject): array;

    public function getRelationships($domainObject): array
    {
        $relationships = $this->getResourceRelationships($domainObject);

        return $this->filter($relationships, $domainObject);
    }

    abstract public function getResourceRelationships($domainObject): array;
}
