<?php

namespace App\JsonApi\Transformer;

use App\Entity\SecureAction;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * SecureAction Resource Transformer.
 */
class SecureActionResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($secureAction): string
    {
        return 'secure-actions';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($secureAction): string
    {
        return (string) $secureAction->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($secureAction): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($secureAction): ?Links
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($secureAction): array
    {
        return [
            'creationDate' => function (SecureAction $secureAction) {
                return $secureAction->getCreationDate()->getTimestamp();
            },
            'expirationDate' => function (SecureAction $secureAction) {
                return $secureAction->getExpirationDate()->getTimestamp();
            },
            'validated' => function (SecureAction $secureAction) {
                return $secureAction->getValidated();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($secureAction): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($secureAction): array
    {
        return [
        ];
    }
}
