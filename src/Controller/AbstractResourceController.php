<?php


namespace App\Controller;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Paknahad\JsonApiBundle\Controller\Controller;
use Paknahad\JsonApiBundle\Helper\ResourceCollection;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\HydratorInterface;
use WoohooLabs\Yin\JsonApi\Hydrator\UpdateRelationshipHydratorInterface;
use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSuccessfulDocument;

abstract class AbstractResourceController extends Controller
{
    abstract protected function getSingleDocument(): AbstractSuccessfulDocument;

    abstract protected function getCollectionDocument(): AbstractSuccessfulDocument;

    abstract protected function getRelatedResponses(): array;

    /**
     * @param ServiceEntityRepository $entityRepository
     * @param ResourceCollection $resourceCollection
     * @return ResponseInterface
     * @throws EntityNotFoundException
     */
    public function resourceIndex(ServiceEntityRepository $entityRepository, ResourceCollection $resourceCollection): ResponseInterface {
        $resourceCollection->setRepository($entityRepository);

        $resourceCollection->handleIndexRequest();

        return $this->jsonApi()->respond()->ok(
            $this->getCollectionDocument(),
            $resourceCollection
        );
    }

    public function resourceNew(object $domainObject, ValidatorInterface $validator, HydratorInterface $hydrator) {
        # TODO : access control

        $domainObject = $this->jsonApi()->hydrate(
            $hydrator,
            $domainObject
        );

        $entityManager = $this->getDoctrine()->getManager();

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($domainObject);
        if ($errors->count() > 0) {
            $entityManager->clear();
            return $this->validationErrorResponse($errors);
        }

        $entityManager->persist($domainObject);
        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            $this->getSingleDocument(),
            $domainObject
        );
    }
    
    public function resourceShow(object $domainObject) {
        # TODO : access control

        return $this->jsonApi()->respond()->ok(
            $this->getSingleDocument(),
            $domainObject
        );
    }

    public function resourceHydrate(object $domainObject, ValidatorInterface $validator, HydratorInterface $hydrator): ResponseInterface {
        # TODO : access control

        $domainObject = $this->jsonApi()->hydrate(
            $hydrator,
            $domainObject
        );

        $entityManager = $this->getDoctrine()->getManager();

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($domainObject);
        if ($errors->count() > 0) {
            $entityManager->clear();
            return $this->validationErrorResponse($errors);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            $this->getSingleDocument(),
            $domainObject
        );
    }

    public function resourceDelete(object $domainObject) {
        # TODO : access control

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($domainObject);
        $entityManager->flush();

        return $this->jsonApi()->respond()->genericSuccess(204);
    }

    public function resourceShowRelationships(object $domainObject) {
        # TODO : access control

        $relationshipName = $this->jsonApi()->getRequest()->getAttribute('rel');
        # TODO : relationship not exist

        return $this->jsonApi()->respond()->okWithRelationship(
            $relationshipName,
            $this->getSingleDocument(),
            $domainObject
        );
    }

    public function resourceHydrateRelationships(object $domainObject, ValidatorInterface $validator, UpdateRelationshipHydratorInterface $hydrator): ResponseInterface
    {
        # TODO : access control

        $relationshipName = $this->jsonApi()->getRequest()->getAttribute('rel');
        # TODO : relationship not exist
        $domainObject = $this->jsonApi()->hydrateRelationship(
            $relationshipName,
            $hydrator,
            $domainObject
        );

        $entityManager = $this->getDoctrine()->getManager();

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($domainObject);
        if ($errors->count() > 0) {
            $entityManager->clear();
            return $this->validationErrorResponse($errors);
        }

        $this->getDoctrine()->getManager()->flush();

        # TODO : 204 No Content
        return $this->jsonApi()->respond()->okWithRelationship(
            $relationshipName,
            $this->getSingleDocument(),
            $domainObject
        );
    }

    public function resourceRelatedEntities(object $domainObject): ResponseInterface
    {
        # TODO : access control

        $relatedResponses = $this->getRelatedResponses();
        $relationshipName = $this->jsonApi()->getRequest()->getAttribute('rel');
        if (!array_key_exists($relationshipName, $relatedResponses)) {
            throw new NotFoundHttpException('relationship not exist');
        }
        return $relatedResponses[$relationshipName]($domainObject, $relationshipName);
    }
}