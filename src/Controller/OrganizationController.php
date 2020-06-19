<?php


namespace App\Controller;


use App\Entity\Organization;
use App\JsonApi\Document\Organization\OrganizationDocument;
use App\JsonApi\Document\Organization\OrganizationRelatedEntitiesDocument;
use App\JsonApi\Document\Organization\OrganizationRelatedEntityDocument;
use App\JsonApi\Document\Organization\OrganizationsDocument;
use App\JsonApi\Hydrator\Organization\CreateOrganizationHydrator;
use App\JsonApi\Hydrator\Organization\CreateRelationshipOrganizationHydrator;
use App\JsonApi\Hydrator\Organization\DeleteRelationshipOrganizationHydrator;
use App\JsonApi\Hydrator\Organization\UpdateOrganizationHydrator;
use App\JsonApi\Hydrator\Organization\UpdateRelationshipOrganizationHydrator;
use App\JsonApi\Transformer\OrganizationResourceTransformer;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\Repository\OrganizationRepository;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument;

/**
 * @Route("/v1/organizations")
 */
class OrganizationController extends AbstractResourceController
{
    protected function getSingleDocument(): AbstractSuccessfulDocument
    {
        return new OrganizationDocument(new OrganizationResourceTransformer());
    }

    protected function getCollectionDocument(): AbstractSuccessfulDocument
    {
        return new OrganizationsDocument(new OrganizationResourceTransformer());
    }

    protected function getRelatedResponses(): array
    {
        return [
            "members" => function (Organization $organization, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new OrganizationRelatedEntitiesDocument(new UserResourceTransformer(), $organization->getId(), $relationshipName),
                    $organization->getMembers()
                );
            },
            "owner" => function (Organization $organization, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new OrganizationRelatedEntityDocument(new UserResourceTransformer(), $organization->getId(), $relationshipName),
                    $organization->getOwner()
                );
            }
        ];
    }

    /**
     * @Route("", name="organizations_index", methods="GET")
     * @param OrganizationRepository $organizationRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     */
    public function index(OrganizationRepository $organizationRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        return $this->resourceIndex(
            $organizationRepository, $resourceCollection
        );
    }

    /**
     * @Route("", name="organizations_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceNew(
            new Organization(), $validator, new CreateOrganizationHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="organizations_show", methods="GET")
     * @param Organization $organization
     * @return ResponseInterface
     */
    public function show(Organization $organization): ResponseInterface
    {
        return $this->resourceShow(
            $organization
        );
    }


    /**
     * @Route("/{id}", name="organizations_edit", methods="PATCH")
     * @param Organization       $organization
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function edit(Organization $organization, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrate(
            $organization, $validator, new UpdateOrganizationHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="organizations_delete", methods="DELETE")
     * @param Organization $organization
     * @return ResponseInterface
     */
    public function delete(Organization $organization): ResponseInterface
    {
        return $this->resourceDelete($organization);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="organizations_relationship", methods="GET")
     * @param Organization $organization
     * @return ResponseInterface
     */
    public function showRelationships(Organization $organization) {
        return $this->resourceShowRelationships($organization);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="organizations_relationships_edit", methods="PATCH")
     * @param Organization $organization
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function editRelationships(Organization $organization, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $organization, $validator, new UpdateRelationshipOrganizationHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="organizations_relationships_new", methods="POST")
     * @param Organization $organization
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function addRelationships(Organization $organization, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $organization, $validator, new CreateRelationshipOrganizationHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="organizations_relationships_delete", methods="DELETE")
     * @param Organization $organization
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function deleteRelationships(Organization $organization, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $organization, $validator, new DeleteRelationshipOrganizationHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}/{rel}", name="organizations_related", methods="GET")
     * @param Organization $organization
     * @return ResponseInterface
     */
    public function showRelatedEntities(Organization $organization) {
        return $this->resourceRelatedEntities($organization);
    }
}