<?php

namespace App\Controller;

use App\Provider\UserProviderInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserController extends AbstractJsonApiController
{
    public function create(Request $request, UserProviderInterface $userProvider)
    {
        // TODO
        if ($request->getContentType() !== 'json')
            throw new BadRequestHttpException('Incorrect json'); // TODO
        $userData = json_decode($request->getContent(), true)['data']['attributes'];
        $user = $userProvider->createUser($userData['email'], $userData['password']);
        return $this->jsonApiResponseProvider->createResponse($user);
    }

    public function getResource(Request $request, int $id, UserRepository $userRepository)
    {
        return $this->jsonApiResponseProvider->createResponse($userRepository->find($id));
    }
}
