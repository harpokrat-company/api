<?php

namespace App\Controller;

use App\Entity\Log;
use App\JsonApi\Document\Log\LogDocument;
use App\JsonApi\Document\Log\LogRelatedEntityDocument;
use App\JsonApi\Document\Log\LogsDocument;
use App\JsonApi\Hydrator\Log\CreateLogHydrator;
use App\JsonApi\Hydrator\Log\UpdateLogHydrator;
use App\JsonApi\Transformer\LogResourceTransformer;
use App\JsonApi\Transformer\UserResourceTransformer;
use App\Repository\LogRepository;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument;

/**
 * @Route("/v1/logs")
 */
class LogController extends AbstractResourceController
{
    protected function getSingleDocument(): AbstractSuccessfulDocument
    {
        return new LogDocument(new LogResourceTransformer());
    }

    protected function getCollectionDocument(): AbstractSuccessfulDocument
    {
        return new LogsDocument(new LogResourceTransformer());
    }

    protected function getRelatedResponses(): array
    {
        return [
            "user" => function (Log $log, string $relationshipName) {
                return $this->jsonApi()->respond()->ok(
                    new LogRelatedEntityDocument(new UserResourceTransformer(), $log->getId(), $relationshipName),
                    $log->getUser()
                );
            }
        ];
    }

    /**
     * @Route("", name="logs_index", methods="GET")
     * @param LogRepository      $logRepository
     * @param ResourceCollection $resourceCollection
     *
     * @return ResponseInterface
     */
    public function index(LogRepository $logRepository, ResourceCollection $resourceCollection): ResponseInterface
    {
        return $this->resourceIndex(
            $logRepository, $resourceCollection
        );
    }

    /**
     * @Route("", name="logs_new", methods="POST")
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function new(ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceNew(
            new Log(), $validator, new CreateLogHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="logs_show", methods="GET")
     * @param Log $log
     * @return ResponseInterface
     */
    public function show(Log $log): ResponseInterface
    {
        return $this->resourceShow(
            $log
        );
    }


    /**
     * @Route("/{id}", name="logs_edit", methods="PATCH")
     * @param Log                $log
     * @param ValidatorInterface $validator
     * @return ResponseInterface
     */
    public function edit(Log $log, ValidatorInterface $validator): ResponseInterface
    {
        return $this->resourceHydrate(
            $log, $validator, new UpdateLogHydrator($this->getDoctrine()->getManager())
        );
    }

    /**
     * @Route("/{id}", name="logs_delete", methods="DELETE")
     * @param Log     $log
     * @return ResponseInterface
     */
    public function delete(Log $log): ResponseInterface
    {
        return $this->resourceDelete($log);
    }

    /**
     * @Route("/{id}/relationships/{rel}", name="logs_relationship", methods="GET")
     * @param Log $log
     * @return ResponseInterface
     */
    public function showRelationships(Log $log) {
        return $this->resourceShowRelationships($log);
    }

    /**
     * @Route("/{id}/{rel}", name="logs_related", methods="GET")
     * @param Log $log
     * @return ResponseInterface
     */
    public function showRelatedEntities(Log $log) {
        return $this->resourceRelatedEntities($log);
    }
}