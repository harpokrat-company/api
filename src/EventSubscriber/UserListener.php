<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Service\EmailValidationService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EmailValidationService
     */
    private $emailValidationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        EmailValidationService $emailValidationService
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->emailValidationService = $emailValidationService;
    }

    private function encodeUserPassword(User $user)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
    }

    private function handleMailModification(User $user)
    {
        $this->emailValidationService->sendValidationMail($user);
    }

    public function prePersist(User $user, LifecycleEventArgs $args)
    {
        $this->encodeUserPassword($user);
        $this->handleMailModification($user);
        return;
    }

    public function preUpdate(User $user, PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('password'))
            $this->encodeUserPassword($user);

        if ($args->hasChangedField('email'))
            $this->handleMailModification($user);
    }
}
