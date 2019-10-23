<?php


namespace App\EventListener;


use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\DependencyInjection\ServiceLocator;

class FlushListener
{
    /**
     * @var ServiceLocator
     */
    private $flushListenerServiceLocator;

    /**
     * FlushListener constructor.
     *
     * @param ServiceLocator $serviceLocator
     */
    public function __construct(ServiceLocator $serviceLocator)
    {
        $this->flushListenerServiceLocator = $serviceLocator;
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $entityManager = $args->getEntityManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            $class = get_class($entity);
            if ($this->flushListenerServiceLocator->has($class)) {
                $this->flushListenerServiceLocator->get($class)->onFlush($entityManager, $unitOfWork, $entity);
            }
        }
    }
}
