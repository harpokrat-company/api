<?php


namespace App\Controller;


use App\Entity\OrganizationGroup;
use App\JsonApi\Document\OrganizationGroup\OrganizationGroupDocument;
use App\JsonApi\Document\OrganizationGroup\OrganizationGroupsDocument;
use App\JsonApi\Hydrator\OrganizationGroup\CreateOrganizationGroupHydrator;
use App\JsonApi\Hydrator\OrganizationGroup\UpdateOrganizationGroupHydrator;
use App\JsonApi\Transformer\OrganizationGroupResourceTransformer;
use App\Repository\OrganizationGroupRepository;
use Doctrine\ORM\EntityNotFoundException;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/v1/groups")
 */
class OrganizationGroupController extends Controller
{
    /**
     * @Route("", name="organization_index", methods="GET")
     * @param OrganizationGroupRepository $groupRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     * @throws EntityNotFoundException
     */
    public function index(OrganizationGroupRepository $groupRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        $resourceCollection->setRepository($groupRepository);

        $resourceCollection->handleIndexRequest();

        return $this->jsonApi()->respond()->ok(
            new OrganizationGroupsDocument(new OrganizationGroupResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("", name="groups_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $group = $this->jsonApi()->hydrate(new CreateOrganizationGroupHydrator($entityManager), new OrganizationGroup());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($group);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->persist($group);
        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new OrganizationGroupDocument(new OrganizationGroupResourceTransformer()),
            $group
        );
    }

    /**
     * @Route("/{id}", name="groups_show", methods="GET")
     * @param OrganizationGroup $group
     * @return ResponseInterface
     */
    public function show(OrganizationGroup $group): ResponseInterface
    {
        return $this->jsonApi()->respond()->ok(
            new OrganizationGroupDocument(new OrganizationGroupResourceTransformer()),
            $group
        );
    }


    /**
     * @Route("/{id}", name="groups_edit", methods="PATCH")
     * @param OrganizationGroup $group
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function edit(OrganizationGroup $group, ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $group = $this->jsonApi()->hydrate(new UpdateOrganizationGroupHydrator($entityManager), $group);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($group);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new OrganizationGroupDocument(new OrganizationGroupResourceTransformer()),
            $group
        );
    }

    /**
     * @Route("/{id}", name="groups_delete", methods="DELETE")
     * @param Request $request
     * @param OrganizationGroup $group
     * @return ResponseInterface
     */
    public function delete(Request $request, OrganizationGroup $group): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($group);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }
}