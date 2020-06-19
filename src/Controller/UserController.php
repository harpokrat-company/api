<?php

namespace App\Controller;

use App\Entity\User;
use App\JsonApi\Document\User\UserDocument;
use App\JsonApi\Document\User\UserRelatedEntitiesDocument;
use App\JsonApi\Document\User\UsersDocument;
use App\JsonApi\Hydrator\User\CreateUserHydrator;
use App\JsonApi\Hydrator\User\UpdateUserHydrator;
use App\JsonApi\Transformer\LogResourceTransformer;
use App\JsonApi\Transformer\OrganizationResourceTransformer;
use App\JsonApi\Transformer\SecretResourceTransformer;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @Route("", name="users_index", methods="GET")
     * @param UserRepository     $userRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     * @throws EntityNotFoundException
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
     * @Route("", name="users_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->jsonApi()->hydrate(new CreateUserHydrator($entityManager), new User());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            $entityManager->clear();
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
     * @param User $user
     * @return ResponseInterface
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
     * @param User               $user
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function edit(User $user, ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->jsonApi()->hydrate(new UpdateUserHydrator($entityManager), $user);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($user);
        if ($errors->count() > 0) {
            $entityManager->clear();
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
     * @param User    $user
     * @return ResponseInterface
     */
    public function delete(User $user): ResponseInterface
    {
        if ($user === $this->getUser()) {
            # TODO : close session and remove jwt
            throw new HttpException(501, "not implemented");
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="users_relationship", methods="GET")
     * @param User $user
     * @return ResponseInterface
     */
    public function showRelationships(User $user) {
        # TODO : access control

        $relationshipName = $this->jsonApi()->getRequest()->getAttribute('rel');
        # TODO : check if relationship exist

        return $this->jsonApi()->respond()->okWithRelationship(
            $relationshipName, new UserDocument(new UserResourceTransformer()), $user
        );
    }


    private function getRelatedResponses() {
        return [
            "organizations" => function (User $user, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new UserRelatedEntitiesDocument(new OrganizationResourceTransformer(), $user->getId(), $relationshipName),
                    $user->getOrganizations()
                );
            },
            "ownedOrganizations" => function (User $user, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new UserRelatedEntitiesDocument(new OrganizationResourceTransformer(), $user->getId(), $relationshipName),
                    $user->getOwnedOrganizations()
                );
            },
            "secrets" => function (User $user, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new UserRelatedEntitiesDocument(new SecretResourceTransformer(), $user->getId(), $relationshipName),
                    $user->getSecrets()
                );
            },
            "logs" => function (User $user, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new UserRelatedEntitiesDocument(new LogResourceTransformer(), $user->getId(), $relationshipName),
                    $user->getLogs()
                );
            },
        ];
    }

    /**
     * @Route("/{id}/{rel}", name="users_related", methods="GET")
     * @param User $user
     * @return ResponseInterface
     */
    public function showRelatedEntities(User $user) {
        # TODO : access control

        $relationshipName = $this->jsonApi()->getRequest()->getAttribute('rel');

        $relatedResponseCreator = $this->getRelatedResponses();
        if (!array_key_exists($relationshipName, $relatedResponseCreator)) {
            throw new NotFoundHttpException('relationship not exist');
        }

        return $relatedResponseCreator[$relationshipName]($user, $relationshipName);
    }
}
