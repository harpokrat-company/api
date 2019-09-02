<?php


namespace App\Service;


use App\Entity\User;

class EmailValidationService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param User $user
     *
     * @throws \Exception
     */
    public function sendValidationMail(User $user)
    {
        $emailValidationCode = bin2hex(random_bytes(64));
        $user->setEmailValidationCode($emailValidationCode);
        $user->setEmailValidationMailSentDate(new \DateTime());
        // TODO
        $mail = new \Swift_Message('Harpokrat: Action required - Email validation'); // TODO Clean mail service && translation
        $mail->setFrom('noreply@harpokrat.com');
        $mail->setTo($user->getEmail());
        $mail->setBody( // TODO Clean renderer using twig with mail service
            '<html><body>Aled oskour !! Validation code: '.$emailValidationCode.'</body></html>',
            'text/html'
        );
        $mail->addPart( // TODO Clean renderer using twig with mail service
            'Aled oskour !! Validation code: '.$emailValidationCode,
            'text/plain'
        );
        $this->mailer->send($mail);
    }
}
