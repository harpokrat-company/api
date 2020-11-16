<?php

namespace App\Controller;

use App\Entity\OrganizationGroup;
use App\Entity\User;
use App\Entity\Vault;
use App\JsonApi\Document\Secret\SecretRelatedEntityDocument;
use App\JsonApi\Document\Vault\VaultDocument;
use App\JsonApi\Document\Vault\VaultRelatedEntitiesDocument;
use App\JsonApi\Document\Vault\VaultRelatedEntityDocument;
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
        return new VaultDocument(new VaultResourceTransformer($this->getAuthorizationChecker()));
    }

    protected function getCollectionDocument(): AbstractSuccessfulDocument
    {
        return new VaultsDocument(new VaultResourceTransformer($this->getAuthorizationChecker()));
    }

    protected function getRelatedResponses(): array
    {
        return [
            'owner' => function (Vault $vault, string $relationshipName) {
                // TODO : abstract this
                $owner = $vault->getOwner();
                if ($owner instanceof User) {
                    $transformer = new UserResourceTransformer($this->getAuthorizationChecker());
                } elseif ($owner instanceof OrganizationGroup) {
                    $transformer = new OrganizationGroupResourceTransformer($this->getAuthorizationChecker());
                } else {
                    throw new LogicException();
                }

                return $this->jsonApi()->respond()->ok(
                    new SecretRelatedEntityDocument($transformer, $vault->getId(), $relationshipName),
                    $owner
                );
            },
            'secrets' => function (Vault $vault, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new VaultRelatedEntitiesDocument(new SecretResourceTransformer($this->getAuthorizationChecker()), $vault->getId(), $relationshipName),
                    $vault->getSecrets()
                );
            },
            'encryption-key' => function (Vault $vault, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new VaultRelatedEntityDocument(new SecretResourceTransformer($this->getAuthorizationChecker()), $vault->getId(), $relationshipName),
                    $vault->getEncryptionKey()
                );
            },
        ];
    }

    /**
     * @Route("", name="vaults_index", methods="GET")
     */
    public function index(VaultRepository $vaultRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        return $this->resourceIndex(
            $vaultRepository, $resourceCollection
        );
    }

    /**
     * @Route("", name="vaults_new", methods="POST")
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceNew(
            new Vault(), $validator, new CreateVaultHydrator($this->getDoctrine()->getManager(), $this->getAuthorizationChecker())
        );
    }

    /**
     * @Route("/{id}", name="vaults_show", methods="GET")
     */
    public function show(Vault $vault): ResponseInterface
    {
        return $this->resourceShow(
            $vault
        );
    }

    /**
     * @Route("/{id}", name="vaults_edit", methods="PATCH")
     */
    public function edit(Vault $vault, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrate(
            $vault, $validator, new UpdateVaultHydrator($this->getDoctrine()->getManager(), $this->getAuthorizationChecker())
        );
    }

    /**
     * @Route("/{id}", name="vaults_delete", methods="DELETE")
     */
    public function delete(Vault $vault): ResponseInterface
    {
        return $this->resourceDelete($vault);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="vaults_relationships_edit", methods="PATCH")
     */
    public function editRelationships(Vault $vault, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrateRelationships(
            $vault, $validator, new UpdateVaultHydrator($this->getDoctrine()->getManager(), $this->getAuthorizationChecker())
        );
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="vaults_relationship", methods="GET")
     *
     * @return ResponseInterface
     */
    public function showRelationships(Vault $vault)
    {
        return $this->resourceShowRelationships($vault);
    }

    /**
     * @Route("/{id}/{rel}", name="vaults_related", methods="GET")
     *
     * @return ResponseInterface
     */
    public function showRelatedEntities(Vault $vault)
    {
        return $this->resourceRelatedEntities($vault);
    }
}
