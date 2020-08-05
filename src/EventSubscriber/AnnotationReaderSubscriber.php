<?php


namespace App\EventSubscriber;


use App\Annotation\ReCaptcha;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class AnnotationReaderSubscriber implements EventSubscriberInterface
{
    const ANNOTATION_EVENTS = [
        ReCaptcha::class => ReCaptchaSubscriber::RECAPTCHA_EVENT,
    ];

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    public function __construct(Reader $annotationReader, EventDispatcherInterface $dispatcher)
    {
        $this->annotationReader = $annotationReader;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ControllerEvent $event
     * @throws ReflectionException
     */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $controllers = $event->getController();
        if (!is_array($controllers)) {
            return;
        }
        $this->handleAnnotation($controllers, $event);
    }

    /**
     * @param iterable $controllers
     * @param $event
     * @throws ReflectionException
     */
    private function handleAnnotation(iterable $controllers, $event): void
    {
        list($controller, $method) = $controllers;
        $controller = new ReflectionClass($controller);
        $method = $controller->getMethod($method);

        foreach (self::ANNOTATION_EVENTS as $name => $eventName) {
            if ($this->annotationReader->getClassAnnotation($controller, $name)) {
                $this->dispatcher->dispatch($event, $eventName);
            }
            if ($this->annotationReader->getMethodAnnotation($method, $name)) {
                $this->dispatcher->dispatch($event, $eventName);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
