<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractJsonApiController
{
    public function createJsonWebToken(Request $request, JWTTokenManagerInterface $jwtManager)
    {
        return $this->jsonApiResponseProvider->createResponse([
            'token' => $jwtManager->create($this->getUser()),
        ]);
    }
}
