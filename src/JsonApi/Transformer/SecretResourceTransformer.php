<?php

namespace App\JsonApi\Transformer;

use App\Entity\OrganizationGroup;
use App\Entity\Secret;
use App\Entity\User;
use App\Entity\Vault;
use LogicException;
use WoohooLabs\Yin\JsonApi\Schema\Link;
use WoohooLabs\Yin\JsonApi\Schema\Links;
use WoohooLabs\Yin\JsonApi\Schema\Relationship\ToOneRelationship;

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
    public function getResourceAttributes($secret): array
    {
        return [
            'content' => function (Secret $secret) {
                return $secret->getContent();
            },
            'private' => function (Secret $secret) {
                return $secret->isPrivate();
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
    public function getResourceRelationships($secret): array
    {
        return [
            'owner' => function (Secret $secret) {
                $owner = $secret->getOwner();
                if ($owner instanceof User) {
                    $transformer = new UserResourceTransformer($this->authorizationChecker);
                } elseif ($owner instanceof Vault) {
                    $transformer = new VaultResourceTransformer($this->authorizationChecker);
                } elseif ($owner instanceof OrganizationGroup) {
                    $transformer = new OrganizationGroupResourceTransformer($this->authorizationChecker);
                } else {
                    throw new LogicException();
                }

                return ToOneRelationship::create()
                    ->setData($owner, $transformer)
                    ->setLinks(Links::createWithoutBaseUri([
                        'self' => new Link('/v1/secrets/'.$secret->getId().'/relationships/owner'),
                        'related' => new Link('/v1/secrets/'.$secret->getId().'/owner'),
                    ]));
            },
        ];
    }
}
