<?php

namespace App\EventSubscriber;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LogSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // TODO Use a clean log provider
        $log = new Log();
        $log->setDate(new \DateTime());
        $log->setIp($event->getRequest()->getClientIp()); // TODO Find a way to log port
        $log->setUri($event->getRequest()->getRequestUri());
        $token = $this->tokenStorage->getToken();
        if (!is_null($token)) {
            $user = $token->getUser();
            if (!is_null($user) && $user instanceof User) {
                $log->setUser($user);
            }
        }
        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }

    public static function getSubscribedEvents()
    {
        // TODO https://rihards.com/2018/symfony-login-event-listener/
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
