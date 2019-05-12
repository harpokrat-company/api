<?php

namespace App\Controller;

use App\Provider\UserProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends AbstractJsonApiController
{
    public function createUser(Request $request, UserProviderInterface $userProvider)
    {
        // TODO
        if ($request->getContentType() !== 'json')
            throw new BadRequestHttpException('Aled'); // TODO
        $userData = json_decode($request->getContent(), true);
        $user = $userProvider->createUser($userData['email'], $userData['password']);
        return $this->jsonApiResponseProvider->createResponse([
            'user' => $user
        ]);
    }
}
