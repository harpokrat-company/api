<?php

namespace App\JsonApi\Transformer;

use App\Entity\User;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Resource\AbstractResource;

/**
 * User Resource Transformer.
 */
class UserResourceTransformer extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function getType($user): string
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function getId($user): string
    {
        return (string) $user->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta($user): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks($user): ?Links
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($user): array
    {
        return [
            'email' => function (User $user) {
                return $user->getEmail();
            },
            'roles' => function (User $user) {
                return $user->getRoles();
            },
            'firstName' => function (User $user) {
                return $user->getFirstName();
            },
            'lastName' => function (User $user) {
                return $user->getLastName();
            },
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultIncludedRelationships($user): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelationships($user): array
    {
        return [
            'secrets' => function (User $user) {
                return ToManyRelationship::create()
                    ->setData($user->getSecrets(), new SecretResourceTransformer())
                    ->omitDataWhenNotIncluded();
            },
            'logs' => function (User $user) {
                return ToManyRelationship::create()
                    ->setData($user->getLogs(), new LogResourceTransformer())
                    ->omitDataWhenNotIncluded();
            },
        ];
    }
}
