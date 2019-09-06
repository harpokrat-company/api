<?php


namespace App\Service;


use App\Entity\SecureAction;
use App\Entity\User;
use App\Provider\SecureActionProvider;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ValidationEmailService
{
    /**
     * @var EmailSenderService
     */
    private $emailSenderService;

    public function __construct(EmailSenderService $emailSenderService)
    {
        $this->emailSenderService = $emailSenderService;
    }

    private function getTemplateVariables(User $user, SecureAction $action)
    {
        return [
            'user' => $user,
            'action' => $action,
            'validation_link' => $action->getValidationLink(),
        ];
    }

    /**
     * @param User         $user
     * @param SecureAction $action
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function sendValidationMail(User $user, SecureAction $action)
    {
        $template = 'validation/' . SecureAction::ACTION_IDENTIFIER[$action->getType()];
        // TODO subject depends on the type -> depends on translations => TODO
        $subject = 'Action needed';
        switch ($action->getType())
        {
            case SecureAction::ACTION_VALIDATE_EMAIL_ADDRESS:
                $subject .= ', email address validation';
                break;
            case SecureAction::ACTION_RESET_PASSWORD:
                $subject .= ', password reset confirmation';
                break;
        }
        $this->emailSenderService->sendMail(
            $user->getEmail(),
            $subject,
            $template,
            $this->getTemplateVariables($user, $action)
        );
    }
}
