<?php

namespace App\Controller;

use App\Entity\User;
use App\JsonApi\Document\User\UserDocument;
use App\JsonApi\Document\User\UserRelatedEntitiesDocument;
use App\JsonApi\Document\User\UsersDocument;
use App\JsonApi\Hydrator\User\CreateRelationshipUserHydrator;
use App\JsonApi\Hydrator\User\CreateUserHydrator;
use App\JsonApi\Hydrator\User\DeleteRelationshipUserHydrator;
use App\JsonApi\Hydrator\User\UpdateRelationshipUserHydrator;
use App\JsonApi\Transformer\LogResourceTransformer;
use App\JsonApi\Transformer\OrganizationResourceTransformer;
use App\JsonApi\Transformer\SecretResourceTransformer;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\Repository\UserRepository;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Document\AbstractSuccessfulDocument;

/**
 * @Route("/v1/users")
 */
class UserController extends AbstractResourceController
{
    protected function getSingleDocument(): AbstractSuccessfulDocument
    {
        return new UserDocument(new UserResourceTransformer());
    }

    protected function getCollectionDocument(): AbstractSuccessfulDocument
    {
        return new UsersDocument(new UserResourceTransformer());
    }

    protected function getRelatedResponses(): array {
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
     * @Route("", name="users_index", methods="GET")
     * @param UserRepository     $userRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     */
    public function index(UserRepository $userRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        return $this->resourceIndex(
            $userRepository, $resourceCollection
        );
    }

    /**
     * @Route("", name="users_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceNew(
            new User(), $validator, new CreateUserHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="users_show", methods="GET")
     * @param User $user
     * @return ResponseInterface
     */
    public function show(User $user): ResponseInterface
    {
        return $this->resourceShow(
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
        return $this->resourceHydrate(
            $user, $validator, new UpdateRelationshipUserHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="users_delete", methods="DELETE")
     * @param User    $user
     * @return ResponseInterface
     */
    public function delete(User $user): ResponseInterface
    {
        return $this->resourceDelete($user);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="users_relationship", methods="GET")
     * @param User $user
     * @return ResponseInterface
     */
    public function showRelationships(User $user) {
        return $this->resourceShowRelationships($user);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="users_relationships_edit", methods="PATCH")
     * @param User $user
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function editRelationships(User $user, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $user, $validator, new UpdateRelationshipUserHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="users_relationships_new", methods="POST")
     * @param User $user
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function addRelationships(User $user, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $user, $validator, new CreateRelationshipUserHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="users_relationships_delete", methods="DELETE")
     * @param User $user
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function deleteRelationships(User $user, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $user, $validator, new DeleteRelationshipUserHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}/{rel}", name="users_related", methods="GET")
     * @param User $user
     * @return ResponseInterface
     */
    public function showRelatedEntities(User $user) {
        return $this->resourceRelatedEntities($user);
    }
}
