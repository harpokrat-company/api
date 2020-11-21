<?php

namespace App\Provider;

use App\Entity\SecureAction;
use App\Entity\User;
use App\Service\ValidationEmailService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SecureActionProvider
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidationEmailService
     */
    private $validationEmailService;

    public function __construct(EntityManagerInterface $entityManager, ValidationEmailService $validationEmailService)
    {
        $this->entityManager = $entityManager;
        $this->validationEmailService = $validationEmailService;
    }

    /**
     * @param $actionType
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function register(User $user, $actionType, array $details = [], DateTime $expirationDate = null)
    {
        if (\is_null($expirationDate)) {
            $expirationDate = new DateTime('+15 minutes');
        }

        $action = new SecureAction();

        $action->setType($actionType);
        $action->setAction($details);
        $action->setCreationDate(new DateTime());
        $action->setExpirationDate($expirationDate);
        $action->setValidated(false);
        $action->setToken(bin2hex(random_bytes(16)));

        $this->entityManager->persist($action);
        $this->entityManager->flush();

        $this->validationEmailService->sendValidationMail($user, $action);
    }

    /**
     * @return null
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function userRegister(User $user, SecureAction $action)
    {
        if (!\in_array($action->getType(), SecureAction::USER_CREATED_ACTIONS)) {
            return null;
        }

        $action->setAction([
            'user_id' => $user->getId(),
            'user_email' => $user->getEmail(),
        ]);
        $action->setCreationDate(new DateTime());
        $action->setExpirationDate(new DateTime('+15 minutes'));
        $action->setValidated(false);
        $action->setToken(bin2hex(random_bytes(16)));

        $this->entityManager->persist($action);
        $this->entityManager->flush();

        $this->validationEmailService->sendValidationMail($user, $action);

        return $action;
    }

    public function mfaRegister(User $user, SecureAction $action)
    {
        if (!$user->isMfaActivated()) {
            return null;
        }

        $action->setAction([
            'user_id' => $user->getId(),
            'user_email' => $user->getEmail(),
        ]);
        $action->setType(SecureAction::ACTION_MFA);
        $action->setCreationDate(new DateTime());
        $action->setExpirationDate(new DateTime('+15 minutes'));
        $action->setValidated(false);
        $action->setToken(sprintf('%06d', rand(0, 10 ** 6 - 1)));

        $this->entityManager->persist($action);
        $this->entityManager->flush();

        $this->validationEmailService->sendValidationMail($user, $action);

        return $action;
    }
}
