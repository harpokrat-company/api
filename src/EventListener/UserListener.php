<?php

namespace App\EventListener;

use App\Entity\SecureAction;
use App\Entity\User;
use App\Provider\SecureActionProvider;
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
     * @var SecureActionProvider
     */
    private $secureActionProvider;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        SecureActionProvider $secureActionProvider
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->secureActionProvider = $secureActionProvider;
    }

    private function encodeUserPassword(User $user)
    {
        $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
    }

    private function handleMailModification(User $user)
    {
        $this->secureActionProvider->register($user, SecureAction::ACTION_VALIDATE_EMAIL_ADDRESS);
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
