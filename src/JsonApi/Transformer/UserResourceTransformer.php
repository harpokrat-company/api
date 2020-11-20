<?php

namespace App\JsonApi\Transformer;

use App\Entity\User;
use App\Entity\Vault;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToManyRelationship;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;

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
    public function getResourceAttributes($user): array
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
            'emailAddressValidated' => function (User $user) {
                return $user->isEmailAddressValidated();
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
    public function getResourceRelationships($user): array
    {
        return [
            'logs' => function (User $user) {
                return ToManyRelationship::create()
                    ->setData($user->getLogs(), new LogResourceTransformer($this->authorizationChecker))
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/users/'.$user->getId().'/relationships/logs'),
                        'related' => new Link('/v1/users/'.$user->getId().'/logs'),
                    ]));
            },
            'organizations' => function (User $user) {
                return ToManyRelationship::create()
                    ->setData($user->getOrganizations(), new OrganizationResourceTransformer($this->authorizationChecker))
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/users/'.$user->getId().'/relationships/organizations'),
                        'related' => new Link('/v1/users/'.$user->getId().'/organizations'),
                    ]));
            },
            'ownedOrganizations' => function (User $user) {
                return ToManyRelationship::create()
                    ->setData($user->getOwnedOrganizations(), new OrganizationResourceTransformer($this->authorizationChecker))
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/users/'.$user->getId().'/relationships/ownedOrganizations'),
                        'related' => new Link('/v1/users/'.$user->getId().'/ownedOrganizations'),
                    ]));
            },
            'secrets' => function (User $user) {
                return ToManyRelationship::create()
                    ->setData($user->getSecrets(), new SecretResourceTransformer($this->authorizationChecker))
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/users/'.$user->getId().'/relationships/secrets'),
                        'related' => new Link('/v1/users/'.$user->getId().'/secrets'),
                    ]));
            },
            'vaults' => function (User $user) {
                return ToManyRelationship::create()
                    ->setData($user->getVaults(), new VaultResourceTransformer($this->authorizationChecker))
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/users/'.$user->getId().'/relationships/vaults'),
                        'related' => new Link('/v1/users/'.$user->getId().'/vaults'),
                    ]));
            },
            'encryption-key' => function (User $vault) {
                return ToOneRelationship::create()
                    ->setData($vault->getEncryptionKey(), new SecretResourceTransformer($this->authorizationChecker))
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/users/'.$vault->getId().'/relationships/encryption-key'),
                        'related' => new Link('/v1/users/'.$vault->getId().'/encryption-key'),
                    ]));
            },
        ];
    }
}
