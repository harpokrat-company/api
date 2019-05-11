<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends AbstractController
{
    public function createJsonWebToken(Request $request, JWTTokenManagerInterface $jwtManager)
    {
        return new JsonResponse([
            'token' => $jwtManager->create($this->getUser()),
        ]);
    }
}
