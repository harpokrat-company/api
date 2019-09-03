<?php


namespace App\Service;


use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class EmailSenderService
{
    const SENDER_EMAIL_ADDRESS = 'noreply@harpokrat.com';

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twigEnvironment)
    {
        $this->mailer = $mailer;
        $this->twig = $twigEnvironment;
    }

    /**
     * @param string $sendTo
     * @param string $subject
     * @param string $template
     * @param array  $variables
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendMail(string $sendTo, string $subject, string $template, array $variables)
    {
        // TODO Translation
        $mail = new \Swift_Message($subject);
        $mail->setFrom(self::SENDER_EMAIL_ADDRESS, 'Harpokrat');
        $mail->setTo($sendTo);
        // TODO Cleaner way to do the following ?
        if ($this->twig->getLoader()->exists('mails/' . $template . '.html.twig')) {
            $mail->setBody(
                $this->twig->render('mails/' . $template . '.html.twig', $variables),
                'text/html'
            );
            if ($this->twig->getLoader()->exists('mails/' . $template . '.txt.twig')) {
                $mail->addPart(
                    $this->twig->render('mails/' . $template . '.txt.twig', $variables),
                    'text/plain'
                );
            }
        } elseif ($this->twig->getLoader()->exists('mails/' . $template . '.txt.twig')) {
            $mail->setBody(
                $this->twig->render('mails/' . $template . '.txt.twig', $variables),
                'text/plain'
            );
        } else {
            throw new LoaderError('No HTML nor TXT twig template found for email type "' .  $template . '"');
        }
        // TODO Sign mail
        $this->mailer->send($mail);
    }
}
