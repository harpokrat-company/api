<?php

namespace App\Service\SecureActionHandler;

use App\Entity\SecureAction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;

class ResetPasswordHandler extends AbstractSecureActionHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \Exception
     */
    public function handleAction(SecureAction $action, UnitOfWork $unitOfWork)
    {
        $user = $this->entityManager->find(User::class, $action->getAction()['user_id']);
        if ($user->getEmail() !== $action->getAction()['user_email']) {
            // TODO Handle cleanly for the user
            throw new \Exception('The email was changed since this action was created.');
        }
        // TODO Change password with token generation or something (creation of new secure action without mail
        // sending ?)
    }
}
