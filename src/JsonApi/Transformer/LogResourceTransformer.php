<?php

namespace App\JsonApi\Transformer;

use App\Entity\Log;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * Log Resource Transformer.
 */
class LogResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($log): string
    {
        return 'logs';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($log): string
    {
        return (string) $log->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($log): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($log): ?Links
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($log): array
    {
        return [
            'date' => function (Log $log) {
                return $log->getDate()->getTimestamp();
            },
            'uri' => function (Log $log) {
                return $log->getUri();
            },
            'ip' => function (Log $log) {
                return $log->getIp();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($log): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($log): array
    {
        return [
            'user' => function (Log $log) {
                return ToOneRelationship::create()
                    ->setData($log->getUser(), new UserResourceTransformer());
            },
        ];
    }
}
