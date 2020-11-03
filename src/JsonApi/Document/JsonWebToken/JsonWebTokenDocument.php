<?php

namespace App\JsonApi\Document\JsonWebToken;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use WoohooLabs\Yin\JsonApi\Document\AbstractSimpleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class JsonWebTokenDocument extends AbstractSimpleResourceDocument
{
    /**
     * @var JWTTokenManagerInterface
     */
    private $JWTTokenManager;

    public function __construct(JWTTokenManagerInterface $JWTTokenManager)
    {
        $this->JWTTokenManager = $JWTTokenManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsonApi(): JsonApiObject
    {
        return new JsonApiObject('1.0');
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks(): ?Links
    {
        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    protected function getResource(): array
    {
        $user = $this->domainObject['user'];
        if (!$user instanceof User) {
            throw new \Exception('Incorrect user given to JWT document');
        }
        $jwt = $this->JWTTokenManager->create($user);

        return [
            'type' => 'json-web-tokens',
            'id' => $this->domainObject['jti'],
            'attributes' => [
                'token' => $jwt,
            ],
            'relationships' => [
                'user' => [
                    'data' => [
                        'type' => 'users',
                        'id' => $user->getId(),
                    ],
                ],
            ],
        ];
    }
}
