<?php

namespace App\JsonApi\Transformer;

use App\Entity\Secret;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Secret Resource Transformer.
 */
class SecretResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($secret): string
    {
        return 'secrets';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($secret): string
    {
        return (string) $secret->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($secret): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($secret): ?Links
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($secret): array
    {
        return [
            'content' => function (Secret $secret) {
                return $secret->getContent();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($secret): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($secret): array
    {
        return [
            'owner' => function (Secret $secret) {
                return ToOneRelationship::create()
                    ->setData($secret->getOwner(), new UserResourceTransformer());
            },
        ];
    }
}
