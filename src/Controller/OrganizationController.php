<?php


namespace App\Controller;


use App\Entity\Organization;
use App\JsonApi\Document\Organization\OrganizationDocument;
use App\JsonApi\Document\Organization\OrganizationsDocument;
use App\JsonApi\Hydrator\Organization\CreateOrganizationHydrator;
use App\JsonApi\Hydrator\Organization\UpdateOrganizationHydrator;
use App\JsonApi\Transformer\OrganizationResourceTransformer;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\EntityNotFoundException;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/v1/organizations")
 */
class OrganizationController extends Controller
{
    /**
     * @Route("", name="organizations_index", methods="GET")
     * @param OrganizationRepository $organizationRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     * @throws EntityNotFoundException
     */
    public function index(OrganizationRepository $organizationRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        $resourceCollection->setRepository($organizationRepository);

        $resourceCollection->handleIndexRequest();

        return $this->jsonApi()->respond()->ok(
            new OrganizationsDocument(new OrganizationResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("", name="organizations_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $organization = $this->jsonApi()->hydrate(new CreateOrganizationHydrator($entityManager), new Organization());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($organization);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->persist($organization);
        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new OrganizationDocument(new OrganizationResourceTransformer()),
            $organization
        );
    }

    /**
     * @Route("/{id}", name="organizations_show", methods="GET")
     * @param Organization $organization
     * @return ResponseInterface
     */
    public function show(Organization $organization): ResponseInterface
    {
        return $this->jsonApi()->respond()->ok(
            new OrganizationDocument(new OrganizationResourceTransformer()),
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
        $entityManager = $this->getDoctrine()->getManager();

        $organization = $this->jsonApi()->hydrate(new UpdateOrganizationHydrator($entityManager), $organization);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($organization);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new OrganizationDocument(new OrganizationResourceTransformer()),
            $organization
        );
    }

    /**
     * @Route("/{id}", name="organizations_delete", methods="DELETE")
     * @param Request $request
     * @param Organization $organization
     * @return ResponseInterface
     */
    public function delete(Request $request, Organization $organization): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($organization);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }
}