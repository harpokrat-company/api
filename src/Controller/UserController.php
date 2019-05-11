<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    public function createUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // TODO
        if (!$request->getContentType() === 'application/json')
            return new JsonResponse([], 400);
        $userData = json_decode($request->getContent(), true);
        $user = new User();
        $user->setEmail($userData['email']);
        $user->setPassword($passwordEncoder->encodePassword($user, $userData['password']));
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return new JsonResponse([
            'user' => $user,
        ]);
    }
}
