<?php


namespace App\Controller;


use App\Entity\OrganizationVault;
use App\JsonApi\Document\OrganizationVault\OrganizationVaultDocument;
use App\JsonApi\Document\OrganizationVault\OrganizationVaultsDocument;
use App\JsonApi\Hydrator\OrganizationVault\CreateOrganizationVaultHydrator;
use App\JsonApi\Hydrator\OrganizationVault\UpdateOrganizationVaultHydrator;
use App\JsonApi\Transformer\OrganizationVaultResourceTransformer;
use App\Repository\OrganizationVaultRepository;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument;

/**
 * @Route("/v1/vaults")
 */
class OrganizationVaultController extends AbstractResourceController
{
    protected function getSingleDocument(): AbstractSuccessfulDocument
    {
        return new OrganizationVaultDocument(new OrganizationVaultResourceTransformer());
    }

    protected function getCollectionDocument(): AbstractSuccessfulDocument
    {
        return new OrganizationVaultsDocument(new OrganizationVaultResourceTransformer());
    }

    protected function getRelatedResponses(): array
    {
        return [
        ];
    }

    /**
     * @Route("", name="vaults_index", methods="GET")
     * @param OrganizationVaultRepository $vaultRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     */
    public function index(OrganizationVaultRepository $vaultRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        return $this->resourceIndex(
            $vaultRepository, $resourceCollection
        );
    }

    /**
     * @Route("", name="vaults_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceNew(
            new OrganizationVault(), $validator, new CreateOrganizationVaultHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="vaults_show", methods="GET")
     * @param OrganizationVault $vault
     * @return ResponseInterface
     */
    public function show(OrganizationVault $vault): ResponseInterface
    {
        return $this->resourceShow(
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
        return $this->resourceHydrate(
            $vault, $validator, new UpdateOrganizationVaultHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="vaults_delete", methods="DELETE")
     * @param OrganizationVault $vault
     * @return ResponseInterface
     */
    public function delete(OrganizationVault $vault): ResponseInterface
    {
        return $this->resourceDelete($vault);
    }
}