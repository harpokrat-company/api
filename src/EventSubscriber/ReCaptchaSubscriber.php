<?php


namespace App\EventSubscriber;


use ReCaptcha\ReCaptcha;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ReCaptchaSubscriber implements EventSubscriberInterface
{
    private $reCaptcha;
    /**
     * @var bool
     */
    private $enable;

    /**
     * ReCaptchaSubscriber constructor.
     * @param ReCaptcha $reCaptcha
     * @param bool $enable
     */
    public function __construct(ReCaptcha $reCaptcha, bool $enable)
    {
        $this->reCaptcha = $reCaptcha;
        $this->enable = $enable;
    }

    public function onKernelController(ControllerEvent $controllerEvent) {
        if (!$this->enable) {
            return;
        }
        $captchaResponse = $controllerEvent->getRequest()->get("g-recaptcha-response");
        if (empty($captchaResponse)) {
            throw new UnauthorizedHttpException(ReCaptcha::SITE_VERIFY_URL, "g-recaptcha-response not found");
        }
        $verify = $this->reCaptcha->verify($captchaResponse);
        if (!$verify->isSuccess()) {
            throw new UnauthorizedHttpException(ReCaptcha::SITE_VERIFY_URL, "g-recaptcha-response " . join($verify->getErrorCodes()));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}