<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractJsonApiController
{
    public function createJsonWebToken(Request $request, JWTTokenManagerInterface $jwtManager)
    {
        // TODO refactorize using Yin / JsonApi things
        return $this->jsonApiResponseProvider->createResponse([
            'type' => 'json-web-tokens',
            'id' => '0',
            'attributes' => [
                'token' => $jwtManager->create($this->getUser()),
            ],
            'relationships' => [
                'user' => $this->getUser(),
            ]
        ]);
    }
}
