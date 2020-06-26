<?php


namespace App\Controller;


use App\Entity\Vault;
use App\JsonApi\Document\Vault\VaultDocument;
use App\JsonApi\Document\Vault\VaultsDocument;
use App\JsonApi\Hydrator\Vault\CreateVaultHydrator;
use App\JsonApi\Hydrator\Vault\UpdateVaultHydrator;
use App\JsonApi\Transformer\VaultResourceTransformer;
use App\Repository\VaultRepository;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument;

/**
 * @Route("/v1/vaults")
 */
class VaultController extends AbstractResourceController
{
    protected function getSingleDocument(): AbstractSuccessfulDocument
    {
        return new VaultDocument(new VaultResourceTransformer());
    }

    protected function getCollectionDocument(): AbstractSuccessfulDocument
    {
        return new VaultsDocument(new VaultResourceTransformer());
    }

    protected function getRelatedResponses(): array
    {
        return [
        ];
    }

    /**
     * @Route("", name="vaults_index", methods="GET")
     * @param VaultRepository $vaultRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     */
    public function index(VaultRepository $vaultRepository, ResourceCollection $resourceCollection): ResponseInterface
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
            new Vault(), $validator, new CreateVaultHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="vaults_show", methods="GET")
     * @param Vault $vault
     * @return ResponseInterface
     */
    public function show(Vault $vault): ResponseInterface
    {
        return $this->resourceShow(
            $vault
        );
    }


    /**
     * @Route("/{id}", name="vaults_edit", methods="PATCH")
     * @param Vault       $vault
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function edit(Vault $vault, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrate(
            $vault, $validator, new UpdateVaultHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="vaults_delete", methods="DELETE")
     * @param Vault $vault
     * @return ResponseInterface
     */
    public function delete(Vault $vault): ResponseInterface
    {
        return $this->resourceDelete($vault);
    }
}