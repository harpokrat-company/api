<?php


namespace App\Service;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PasswordResetService
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
    public function sendPasswordResetMail(User $user)
    {
        $passwordResetToken = bin2hex(random_bytes(32));
        // TODO stuff using token to later use it (Request for action or something. Change in Email validation too)
        $this->mailer->sendMail(
            $user->getEmail(),
            'Password reset request',
            'password_reset',
            [
                'reset_token' => $passwordResetToken,
            ]
        );
    }
}
