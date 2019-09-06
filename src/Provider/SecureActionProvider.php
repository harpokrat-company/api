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
     * @param User           $user
     * @param                $actionType
     * @param array          $details
     * @param DateTime|null  $expirationDate
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws \Exception
     */
    public function register(User $user, $actionType, array $details = [], DateTime $expirationDate = null)
    {
        if (is_null($expirationDate)) {
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
}
