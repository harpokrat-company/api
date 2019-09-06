<?php


namespace App\Service\SecureActionHandler;


use App\Entity\SecureAction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ValidateEmailAddressHandler extends AbstractSecureActionHandler
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
     * @param SecureAction $action
     *
     * @throws \Exception
     */
    public function handleAction(SecureAction $action)
    {
        $user = $this->entityManager->find(User::class, $action->getAction()['user_id']);
        if ($user->getEmail() !== $action->getAction()['user_email']) {
            // TODO Handle cleanly for the user
            throw new \Exception('The email was changed since this action was created.');
        }
        $user->setEmailAddressValidated(true);
        var_dump($user->isEmailAddressValidated());
    }
}
