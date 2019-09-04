<?php

namespace App\Controller;

use App\Entity\Secret;
use App\JsonApi\Document\Secret\SecretDocument;
use App\JsonApi\Document\Secret\SecretsDocument;
use App\JsonApi\Hydrator\Secret\CreateSecretHydrator;
use App\JsonApi\Hydrator\Secret\UpdateSecretHydrator;
use App\JsonApi\Transformer\SecretResourceTransformer;
use App\Repository\SecretRepository;
use Doctrine\ORM\EntityNotFoundException;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/v1/secrets")
 */
class SecretController extends Controller
{
    /**
     * @Route("/", name="secrets_index", methods="GET")
     * @param SecretRepository   $secretRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     * @throws EntityNotFoundException
     */
    public function index(SecretRepository $secretRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        $resourceCollection->setRepository($secretRepository);

        $resourceCollection->handleIndexRequest();

        return $this->jsonApi()->respond()->ok(
            new SecretsDocument(new SecretResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="secrets_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $secret = $this->jsonApi()->hydrate(new CreateSecretHydrator($entityManager), new Secret());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($secret);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->persist($secret);
        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new SecretDocument(new SecretResourceTransformer()),
            $secret
        );
    }

    /**
     * @Route("/{id}", name="secrets_show", methods="GET")
     * @param Secret $secret
     * @return ResponseInterface
     */
    public function show(Secret $secret): ResponseInterface
    {
        return $this->jsonApi()->respond()->ok(
            new SecretDocument(new SecretResourceTransformer()),
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
        $entityManager = $this->getDoctrine()->getManager();

        $secret = $this->jsonApi()->hydrate(new UpdateSecretHydrator($entityManager), $secret);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($secret);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new SecretDocument(new SecretResourceTransformer()),
            $secret
        );
    }

    /**
     * @Route("/{id}", name="secrets_delete", methods="DELETE")
     * @param Request $request
     * @param Secret  $secret
     * @return ResponseInterface
     */
    public function delete(Request $request, Secret $secret): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($secret);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }
}
