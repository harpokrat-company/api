<?php

namespace App\EventListener;

use App\Entity\SecureAction;
use App\Service\SecureActionHandler\AbstractSecureActionHandler;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SecureActionListener
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ServiceLocator
     */
    private $secureActionHandlersLocator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ServiceLocator $secureActionHandlersLocator
    )
    {
        $this->entityManager = $entityManager;
        $this->secureActionHandlersLocator = $secureActionHandlersLocator;
    }

    /**
     * @param SecureAction       $action
     * @param PreUpdateEventArgs $args
     *
     * @throws \Exception
     */
    public function preUpdate(SecureAction $action, PreUpdateEventArgs $args)
    {
        if ($args->hasChangedField('validated') && $action->getValidated()) {
            $handler = $this->secureActionHandlersLocator->get($action->getType());

            if (!$handler instanceof AbstractSecureActionHandler)
                throw new \Exception('Unhandled SecureAction');

            $handler->handleAction($action);
        }
    }
}
