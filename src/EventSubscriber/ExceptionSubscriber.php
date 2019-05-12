<?php

namespace App\EventSubscriber;

use App\Provider\JsonApiResponseProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var JsonApiResponseProviderInterface
     */
    private $jsonApiResponseProvider;

    public function __construct(JsonApiResponseProviderInterface $jsonApiResponseProvider)
    {
        $this->jsonApiResponseProvider = $jsonApiResponseProvider;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $event->setResponse(
            $this->jsonApiResponseProvider->createErrorResponse(
                500,
                $event->getException()->getMessage()
            )
        );
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
