<?php

namespace App\EventListener;

use App\Entity\SecureAction;
use App\Service\SecureActionHandler\AbstractSecureActionHandler;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\DependencyInjection\ServiceLocator;

class SecureActionListener
{
    /**
     * @var ServiceLocator
     */
    private $secureActionHandlersLocator;

    public function __construct(ServiceLocator $secureActionHandlersLocator)
    {
        $this->secureActionHandlersLocator = $secureActionHandlersLocator;
    }

    /**
     * @param EntityManager $entityManager
     * @param UnitOfWork    $unitOfWork
     * @param SecureAction  $action
     *
     * @throws \Exception
     */
    public function onFlush(EntityManager $entityManager, UnitOfWork $unitOfWork, SecureAction $action)
    {
        $changes = $unitOfWork->getEntityChangeSet($action);
        if (key_exists('validated', $changes) && $action->getValidated()) {
            $handler = $this->secureActionHandlersLocator->get($action->getType());

            if (!$handler instanceof AbstractSecureActionHandler)
                throw new \Exception('Unhandled SecureAction');

            $handler->handleAction($action, $unitOfWork);
        }
    }
}
