<?php

namespace App\Controller;

use App\Entity\OrganizationGroup;
use App\JsonApi\Document\OrganizationGroup\OrganizationGroupDocument;
use App\JsonApi\Document\OrganizationGroup\OrganizationGroupRelatedEntitiesDocument;
use App\JsonApi\Document\OrganizationGroup\OrganizationGroupRelatedEntityDocument;
use App\JsonApi\Document\OrganizationGroup\OrganizationGroupsDocument;
use App\JsonApi\Hydrator\OrganizationGroup\CreateOrganizationGroupHydrator;
use App\JsonApi\Hydrator\OrganizationGroup\CreateRelationshipOrganizationGroupHydrator;
use App\JsonApi\Hydrator\OrganizationGroup\DeleteRelationshipOrganizationGroupHydrator;
use App\JsonApi\Hydrator\OrganizationGroup\UpdateOrganizationGroupHydrator;
use App\JsonApi\Hydrator\OrganizationGroup\UpdateRelationshipOrganizationGroupHydrator;
use App\JsonApi\Transformer\OrganizationGroupResourceTransformer;
use App\JsonApi\Transformer\OrganizationResourceTransformer;
use App\JsonApi\Transformer\SecretResourceTransformer;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\JsonApi\Transformer\VaultResourceTransformer;
use App\Repository\OrganizationGroupRepository;
use Doctrine\ORM\EntityNotFoundException;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument;

/**
 * @Route("/v1/groups")
 */
class OrganizationGroupController extends AbstractResourceController
{
    protected function getSingleDocument(): AbstractSuccessfulDocument
    {
        return new OrganizationGroupDocument(new OrganizationGroupResourceTransformer($this->getAuthorizationChecker()));
    }

    protected function getCollectionDocument(): AbstractSuccessfulDocument
    {
        return new OrganizationGroupsDocument(new OrganizationGroupResourceTransformer($this->getAuthorizationChecker()));
    }

    protected function getRelatedResponses(): array
    {
        return [
            'children' => function (OrganizationGroup $group, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new OrganizationGroupRelatedEntitiesDocument(new OrganizationGroupResourceTransformer($this->getAuthorizationChecker()), $group->getId(), $relationshipName),
                    $group->getChildren()
                );
            },
            'members' => function (OrganizationGroup $group, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new OrganizationGroupRelatedEntitiesDocument(new UserResourceTransformer($this->getAuthorizationChecker()), $group->getId(), $relationshipName),
                    $group->getMembers()
                );
            },
            'organization' => function (OrganizationGroup $group, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new OrganizationGroupRelatedEntityDocument(new OrganizationResourceTransformer($this->getAuthorizationChecker()), $group->getId(), $relationshipName),
                    $group->getOrganization()
                );
            },
            'parent' => function (OrganizationGroup $group, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new OrganizationGroupRelatedEntityDocument(new OrganizationGroupResourceTransformer($this->getAuthorizationChecker()), $group->getId(), $relationshipName),
                    $group->getParent()
                );
            },
            'secrets' => function (OrganizationGroup $group, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new OrganizationGroupRelatedEntitiesDocument(new SecretResourceTransformer($this->getAuthorizationChecker()), $group->getId(), $relationshipName),
                    $group->getSecrets()
                );
            },
            'vaults' => function (OrganizationGroup $group, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new OrganizationGroupRelatedEntitiesDocument(new VaultResourceTransformer($this->getAuthorizationChecker()), $group->getId(), $relationshipName),
                    $group->getVaults()
                );
            },
        ];
    }

    /**
     * @Route("", name="groups_index", methods="GET")
     *
     * @throws EntityNotFoundException
     */
    public function index(OrganizationGroupRepository $groupRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        return $this->resourceIndex(
            $groupRepository, $resourceCollection
        );
    }

    /**
     * @Route("", name="groups_new", methods="POST")
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceNew(
            new OrganizationGroup(), $validator, new CreateOrganizationGroupHydrator($this->getDoctrine()->getManager(), $this->getAuthorizationChecker())
        );
    }

    /**
     * @Route("/{id}", name="groups_show", methods="GET")
     */
    public function show(OrganizationGroup $group): ResponseInterface
    {
        return $this->resourceShow(
            $group
        );
    }

    /**
     * @Route("/{id}", name="groups_edit", methods="PATCH")
     */
    public function edit(OrganizationGroup $group, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrate(
            $group, $validator, new UpdateOrganizationGroupHydrator($this->getDoctrine()->getManager(), $this->getAuthorizationChecker())
        );
    }

    /**
     * @Route("/{id}", name="groups_delete", methods="DELETE")
     */
    public function delete(OrganizationGroup $group): ResponseInterface
    {
        return $this->resourceDelete($group);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="groups_relationship", methods="GET")
     *
     * @return ResponseInterface
     */
    public function showRelationships(OrganizationGroup $group)
    {
        return $this->resourceShowRelationships($group);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="groups_relationships_edit", methods="PATCH")
     */
    public function editRelationships(OrganizationGroup $group, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $group, $validator, new UpdateRelationshipOrganizationGroupHydrator($this->getDoctrine()->getManager(), $this->getAuthorizationChecker())
        );
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="groups_relationships_new", methods="POST")
     */
    public function addRelationships(OrganizationGroup $group, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $group, $validator, new CreateRelationshipOrganizationGroupHydrator($this->getDoctrine()->getManager(), $this->getAuthorizationChecker())
        );
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="groups_relationships_delete", methods="DELETE")
     */
    public function deleteRelationships(OrganizationGroup $group, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $group, $validator, new DeleteRelationshipOrganizationGroupHydrator($this->getDoctrine()->getManager(), $this->getAuthorizationChecker())
        );
    }

    /**
     * @Route("/{id}/{rel}", name="groups_related", methods="GET")
     *
     * @return ResponseInterface
     */
    public function showRelatedEntities(OrganizationGroup $group)
    {
        return $this->resourceRelatedEntities($group);
    }
}
