<?php

namespace App\Controller;

use App\Entity\SecureAction;
use App\Entity\User;
use App\JsonApi\Document\SecureAction\SecureActionDocument;
use App\JsonApi\Hydrator\SecureAction\CreateSecureActionHydrator;
use App\JsonApi\Hydrator\SecureAction\UpdateSecureActionHydrator;
use App\JsonApi\Transformer\SecureActionResourceTransformer;
use App\Provider\SecureActionProvider;
use App\Repository\UserRepository;
use Paknahad\JsonApiBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use WoohooLabs\Yin\JsonApi\Document\ErrorDocument;
use WoohooLabs\Yin\JsonApi\Schema\Error\Error;
use WoohooLabs\Yin\JsonApi\Schema\Error\ErrorSource;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;

/**
 * @Route("/v1/secure-actions")
 */
class SecureActionController extends Controller
{
    /**
     * @Route("/{id}", name="secure_actions_show", methods="GET")
     * @param SecureAction $secureAction
     * @return ResponseInterface
     */
    public function show(SecureAction $secureAction): ResponseInterface
    {
        return $this->jsonApi()->respond()->ok(
            new SecureActionDocument(new SecureActionResourceTransformer()),
            $secureAction
        );
    }

    /**
     * @Route("", name="secure_actions_new", methods="POST")
     * @param ValidatorInterface $validator
     * @param SecureActionProvider $secureActionProvider
     *
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function new(ValidatorInterface $validator, SecureActionProvider $secureActionProvider,
                        UserRepository $userRepository): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        $secureAction = $this->jsonApi()->hydrate(new CreateSecureActionHydrator($entityManager), new SecureAction());

        $user = $this->getUser();
        $anonymous = false;
        if (!$user) {
            $user = $userRepository->findOneBy(['email'=> $secureAction->getPayload()]);
            $anonymous = true;
        }
        if (!$user instanceof User) {
            throw new NotFoundHttpException();
        }

        $secureAction = $secureActionProvider->userRegister($user, $secureAction, $anonymous);
        if (is_null($secureAction)) {
            throw new UnauthorizedHttpException('Bearer');
        }

        return $this->jsonApi()->respond()->ok(
            new SecureActionDocument(new SecureActionResourceTransformer()),
            $secureAction
        );
    }

    /**
     * @Route("/{id}", name="secure_actions_edit", methods="PATCH")
     * @param Request $request
     * @param SecureAction $secureAction
     * @param ValidatorInterface $validator
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function edit(Request $request, SecureAction $secureAction, ValidatorInterface $validator): ResponseInterface
    {
        $entityManager = $this->getDoctrine()->getManager();

        // TODO Cleaner ? && For delete too
        $content = json_decode($request->getContent(), true);
        if (empty($content['meta'])
            || empty($content['meta']['token'])
            || $content['meta']['token'] !== $secureAction->getToken()
        ) {
            $errorDocument = new ErrorDocument();
            $errorDocument->setJsonApi(new JsonApiObject('1.0'));

            $error = Error::create();
            $error->setSource(new ErrorSource(
                '/meta/token',
                'Invalid value'
            ));
            $error->setDetail('Incorrect verification token value');
            $error->setStatus('401');

            $errorDocument->addError($error);

            return $this->jsonApi()->respond()->genericError($errorDocument, [], 401);
        }

        $secureAction = $this->jsonApi()->hydrate(new UpdateSecureActionHydrator($entityManager), $secureAction);

        /** @var ConstraintViolationList $errors */
        $errors = $validator->validate($secureAction);
        if ($errors->count() > 0) {
            $entityManager->clear();
            return $this->validationErrorResponse($errors);
        }

        if ($secureAction->getExpirationDate() < new \DateTime()) {
            $errorDocument = new ErrorDocument();
            $errorDocument->setJsonApi(new JsonApiObject('1.0'));

            $error = Error::create();
            $error->setDetail('This action is expired');
            $error->setStatus('401');

            $errorDocument->addError($error);

            return $this->jsonApi()->respond()->genericError($errorDocument, [], 410);
        }

        $entityManager->flush();

        return $this->jsonApi()->respond()->ok(
            new SecureActionDocument(new SecureActionResourceTransformer()),
            $secureAction
        );
    }

    /**
     * @Route("/{id}", name="secure_actions_delete", methods="DELETE")
     * @param Request $request
     * @param SecureAction $secureAction
     * @return ResponseInterface
     */
//    public function delete(Request $request, SecureAction $secureAction): ResponseInterface
//    {
//        $entityManager = $this->getDoctrine()->getManager();
//        $entityManager->remove($secureAction);
//        $entityManager->flush();
//
//        return $this->jsonApi()->respond()->genericSuccess(204);
//    }
}
