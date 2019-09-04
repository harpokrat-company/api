<?php

namespace App\Controller;

use App\Entity\User;
use App\JsonApi\Document\User\UserDocument;
use App\JsonApi\Document\User\UsersDocument;
use App\JsonApi\Hydrator\User\CreateUserHydrator;
use App\JsonApi\Hydrator\User\UpdateUserHydrator;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\Repository\UserRepository;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/v1/users")
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="users_index", methods="GET")
     */
    public function index(UserRepository $userRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        $resourceCollection->setRepository($userRepository);

        $resourceCollection->handleIndexRequest();

        return $this->jsonApi()->respond()->ok(
            new UsersDocument(new UserResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="users_new", methods="POST")
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->jsonApi()->hydrate(new CreateUserHydrator($entityManager), new User());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    /**
     * @Route("/{id}", name="users_show", methods="GET")
     */
    public function show(User $user): ResponseInterface
    {
        return $this->jsonApi()->respond()->ok(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    /**
     * @Route("/{id}", name="users_edit", methods="PATCH")
     */
    public function edit(User $user, ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->jsonApi()->hydrate(new UpdateUserHydrator($entityManager), $user);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new UserDocument(new UserResourceTransformer()),
            $user
        );
    }

    /**
     * @Route("/{id}", name="users_delete", methods="DELETE")
     */
    public function delete(Request $request, User $user): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }
}
