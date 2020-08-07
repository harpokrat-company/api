<?php


namespace App\EventSubscriber;


use ReCaptcha\ReCaptcha;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;

class ReCaptchaSubscriber implements EventSubscriberInterface
{
    const RECAPTCHA_EVENT = "harpokrat.api.jsonapi.recaptcha";

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

    public function onJsonApiRecaptcha(JsonApiRequest $request) {
        if (!$this->enable) {
            return;
        }
        $captchaResponse = $request->getParsedBody()['meta']['captcha'];
        if (empty($captchaResponse)) {
            throw new UnauthorizedHttpException(ReCaptcha::SITE_VERIFY_URL, "captcha response not found");
        }
        $verify = $this->reCaptcha->verify($captchaResponse);
        if (!$verify->isSuccess()) {
            throw new UnauthorizedHttpException(ReCaptcha::SITE_VERIFY_URL, "captcha response " . join($verify->getErrorCodes()));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            self::RECAPTCHA_EVENT => 'onJsonApiRecaptcha',
        ];
    }
}
