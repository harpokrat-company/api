<?php

namespace App\Controller;

use App\Entity\Log;
use App\JsonApi\Document\Log\LogDocument;
use App\JsonApi\Document\Log\LogsDocument;
use App\JsonApi\Hydrator\Log\CreateLogHydrator;
use App\JsonApi\Hydrator\Log\UpdateLogHydrator;
use App\JsonApi\Transformer\LogResourceTransformer;
use App\Repository\LogRepository;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/v1/logs")
 */
class LogController extends Controller
{
    /**
     * @Route("/", name="logs_index", methods="GET")
     */
    public function index(LogRepository $logRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        $resourceCollection->setRepository($logRepository);

        $resourceCollection->handleIndexRequest();

        return $this->jsonApi()->respond()->ok(
            new LogsDocument(new LogResourceTransformer()),
            $resourceCollection
        );
    }

    /**
     * @Route("/", name="logs_new", methods="POST")
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $log = $this->jsonApi()->hydrate(new CreateLogHydrator($entityManager), new Log());

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($log);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->persist($log);
        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new LogDocument(new LogResourceTransformer()),
            $log
        );
    }

    /**
     * @Route("/{id}", name="logs_show", methods="GET")
     */
    public function show(Log $log): ResponseInterface
    {
        return $this->jsonApi()->respond()->ok(
            new LogDocument(new LogResourceTransformer()),
            $log
        );
    }

    /**
     * @Route("/{id}", name="logs_edit", methods="PATCH")
     */
    public function edit(Log $log, ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $log = $this->jsonApi()->hydrate(new UpdateLogHydrator($entityManager), $log);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($log);
        if ($errors->count() > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new LogDocument(new LogResourceTransformer()),
            $log
        );
    }

    /**
     * @Route("/{id}", name="logs_delete", methods="DELETE")
     */
    public function delete(Request $request, Log $log): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($log);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }
}
