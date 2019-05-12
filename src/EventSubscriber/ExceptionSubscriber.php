<?php

namespace App\EventSubscriber;

use App\Model\JsonApiDocument;
use App\Model\JsonApiError;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $document = new JsonApiDocument();
        $error = new JsonApiError();
        $error->setDetail($exception->getMessage());
        $error->setPointer('/data/attributes/pointer');
        $document->setErrors([
            $error,
        ]);
        $document->setData(['Aled'=>'oskour']);
        $response = new Response($this->serializer->serialize($document, 'json'));
        // TODO Exception handling
        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
