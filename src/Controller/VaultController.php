<?php


namespace App\Controller;


use App\Entity\OrganizationGroup;
use App\Entity\User;
use App\Entity\Vault;
use App\JsonApi\Document\Secret\SecretRelatedEntityDocument;
use App\JsonApi\Document\Vault\VaultDocument;
use App\JsonApi\Document\Vault\VaultRelatedEntitiesDocument;
use App\JsonApi\Document\Vault\VaultsDocument;
use App\JsonApi\Hydrator\Vault\CreateVaultHydrator;
use App\JsonApi\Hydrator\Vault\UpdateVaultHydrator;
use App\JsonApi\Transformer\OrganizationGroupResourceTransformer;
use App\JsonApi\Transformer\SecretResourceTransformer;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\JsonApi\Transformer\VaultResourceTransformer;
use App\Repository\VaultRepository;
use LogicException;
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
            "owner" => function (Vault $vault, string $relationshipName) {
                # TODO : abstract this
                $owner = $vault->getOwner();
                if ($owner instanceof User)
                    $transformer = new UserResourceTransformer();
                else if ($owner instanceof OrganizationGroup)
                    $transformer = new OrganizationGroupResourceTransformer();
                else
                    throw new LogicException();
                return $this->jsonApi()->respond()->ok(
                    new SecretRelatedEntityDocument($transformer, $vault->getId(), $relationshipName),
                    $owner
                );
            },
            "secrets" => function (Vault $vault, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new VaultRelatedEntitiesDocument(new SecretResourceTransformer(), $vault->getId(), $relationshipName),
                    $vault->getSecrets()
                );
            },
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

    /**
     * @Route("/{id}/relationships/{rel}", name="vaults_relationship", methods="GET")
     * @param Vault $vault
     * @return ResponseInterface
     */
    public function showRelationships(Vault $vault) {
        return $this->resourceShowRelationships($vault);
    }

    /**
     * @Route("/{id}/{rel}", name="vaults_related", methods="GET")
     * @param Vault $vault
     * @return ResponseInterface
     */
    public function showRelatedEntities(Vault $vault) {
        return $this->resourceRelatedEntities($vault);
    }
}