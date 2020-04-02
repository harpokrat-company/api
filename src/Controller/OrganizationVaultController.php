<?php


namespace App\Controller;


use App\Entity\OrganizationVault;
use App\JsonApi\Document\OrganizationVault\OrganizationVaultDocument;
use App\JsonApi\Document\OrganizationVault\OrganizationVaultsDocument;
use App\JsonApi\Hydrator\OrganizationVault\CreateOrganizationVaultHydrator;
use App\JsonApi\Hydrator\OrganizationVault\UpdateOrganizationVaultHydrator;
use App\JsonApi\Transformer\OrganizationVaultResourceTransformer;
use App\Repository\OrganizationVaultRepository;
use Doctrine\ORM\EntityNotFoundException;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/v1/vaults")
 */
class OrganizationVaultController extends Controller
{
    /**
     * @Route("", name="vaults_index", methods="GET")
     * @param OrganizationVaultRepository $vaultRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     * @throws EntityNotFoundException
     */
    public function index(OrganizationVaultRepository $vaultRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        $resourceCollection->setRepository($vaultRepository);

        $resourceCollection->handleIndexRequest();

        return $this->jsonApi()->respond()->ok(
            new OrganizationVaultsDocument(new OrganizationVaultResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("", name="vaults_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $vault = $this->jsonApi()->hydrate(new CreateOrganizationVaultHydrator($entityManager), new OrganizationVault());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($vault);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->persist($vault);
        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new OrganizationVaultDocument(new OrganizationVaultResourceTransformer()),
            $vault
        );
    }

    /**
     * @Route("/{id}", name="vaults_show", methods="GET")
     * @param OrganizationVault $vault
     * @return ResponseInterface
     */
    public function show(OrganizationVault $vault): ResponseInterface
    {
        return $this->jsonApi()->respond()->ok(
            new OrganizationVaultDocument(new OrganizationVaultResourceTransformer()),
            $vault
        );
    }


    /**
     * @Route("/{id}", name="vaults_edit", methods="PATCH")
     * @param OrganizationVault       $vault
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function edit(OrganizationVault $vault, ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $vault = $this->jsonApi()->hydrate(new UpdateOrganizationVaultHydrator($entityManager), $vault);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($vault);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new OrganizationVaultDocument(new OrganizationVaultResourceTransformer()),
            $vault
        );
    }

    /**
     * @Route("/{id}", name="vaults_delete", methods="DELETE")
     * @param Request $request
     * @param OrganizationVault $vault
     * @return ResponseInterface
     */
    public function delete(Request $request, OrganizationVault $vault): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($vault);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }
}