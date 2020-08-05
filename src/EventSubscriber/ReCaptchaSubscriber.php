<?php


namespace App\EventSubscriber;


use ReCaptcha\ReCaptcha;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ReCaptchaSubscriber implements EventSubscriberInterface
{
    const RECAPTCHA_EVENT = "harpokrat.api.controller.recaptcha";

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

    public function onRecaptchaController(ControllerEvent $controllerEvent) {
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
            self::RECAPTCHA_EVENT => 'onRecaptchaController',
        ];
    }
}
