<?php

namespace App\Controller;

use App\Entity\Secret;
use App\JsonApi\Document\Secret\SecretDocument;
use App\JsonApi\Document\Secret\SecretRelatedEntityDocument;
use App\JsonApi\Document\Secret\SecretsDocument;
use App\JsonApi\Hydrator\Secret\CreateSecretHydrator;
use App\JsonApi\Hydrator\Secret\UpdateSecretHydrator;
use App\JsonApi\Transformer\SecretResourceTransformer;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\Repository\SecretRepository;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument;

/**
 * @Route("/v1/secrets")
 */
class SecretController extends AbstractResourceController
{
    protected function getSingleDocument(): AbstractSuccessfulDocument
    {
        return new SecretDocument(new SecretResourceTransformer());
    }

    protected function getCollectionDocument(): AbstractSuccessfulDocument
    {
        return new SecretsDocument(new SecretResourceTransformer());
    }

    protected function getRelatedResponses(): array
    {
        return [
            "owner" => function (Secret $secret, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new SecretRelatedEntityDocument(new UserResourceTransformer(), $secret->getId(), $relationshipName),
                    $secret->getOwner()
                );
            }
        ];
    }

    /**
     * @Route("", name="secrets_index", methods="GET")
     * @param SecretRepository   $secretRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     */
    public function index(SecretRepository $secretRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        return $this->resourceIndex(
            $secretRepository, $resourceCollection
        );
    }

    /**
     * @Route("", name="secrets_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceNew(
            new Secret(), $validator, new CreateSecretHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="secrets_show", methods="GET")
     * @param Secret $secret
     * @return ResponseInterface
     */
    public function show(Secret $secret): ResponseInterface
    {
        return $this->resourceShow(
            $secret
        );
    }


    /**
     * @Route("/{id}", name="secrets_edit", methods="PATCH")
     * @param Secret             $secret
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function edit(Secret $secret, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrate(
            $secret, $validator, new UpdateSecretHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="secrets_delete", methods="DELETE")
     * @param Secret  $secret
     * @return ResponseInterface
     */
    public function delete(Secret $secret): ResponseInterface
    {
        return $this->resourceDelete($secret);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="secrets_relationship", methods="GET")
     * @param Secret $secret
     * @return ResponseInterface
     */
    public function showRelationships(Secret $secret) {
        return $this->resourceShowRelationships($secret);
    }

    /**
     * @Route("/{id}/{rel}", name="secrets_related", methods="GET")
     * @param Secret $secret
     * @return ResponseInterface
     */
    public function showRelatedEntities(Secret $secret) {
        return $this->resourceRelatedEntities($secret);
    }
}