<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class EmailValidationService
{
    /**
     * @var EmailSenderService
     */
    private $mailer;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EmailSenderService $mailer, EntityManagerInterface $entityManager)
    {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function sendValidationMail(User $user)
    {
        $emailValidationCode = bin2hex(random_bytes(32));
        // TODO use validation code somehow
        $this->mailer->sendMail(
            $user->getEmail(),
            'Action needed: email address validation',
            'email_address_validation',
            [
                'validation_code' => $emailValidationCode,
            ]
        );
    }
}
