<?php

namespace App\EventSubscriber;

use ReCaptcha\ReCaptcha;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use WoohooLabs\Yin\JsonApi\Request\JsonApiRequest;

class ReCaptchaSubscriber implements EventSubscriberInterface
{
    const RECAPTCHA_TYPE_ANDROID = 'reCAPTCHA-v2-android';
    const RECAPTCHA_TYPE_TICKBOX = 'reCAPTCHA-v2-tickbox';

    const RECAPTCHA_EVENT = 'harpokrat.api.jsonapi.recaptcha';

    private $reCaptchaTickbox;
    /**
     * @var bool
     */
    private $enable;
    /**
     * @var ReCaptcha
     */
    private $reCaptchaAndroid;

    /**
     * ReCaptchaSubscriber constructor.
     */
    public function __construct(ReCaptcha $reCaptchaTickbox, ReCaptcha $reCaptchaAndroid, bool $enable)
    {
        $this->reCaptchaTickbox = $reCaptchaTickbox;
        $this->reCaptchaAndroid = $reCaptchaAndroid;
        $this->enable = $enable;
    }

    public function onJsonApiRecaptcha(JsonApiRequest $request)
    {
        if (!$this->enable) {
            return;
        }
        $recaptchaByType = [
            self::RECAPTCHA_TYPE_TICKBOX => $this->reCaptchaTickbox,
            self::RECAPTCHA_TYPE_ANDROID => $this->reCaptchaAndroid,
        ];
        $captchaResponse = $request->getParsedBody()['meta']['captcha'];
        if (empty($captchaResponse) || !isset($captchaResponse['type'], $recaptchaByType[$captchaResponse['type']], $captchaResponse['response'])) {
            throw new UnauthorizedHttpException(ReCaptcha::SITE_VERIFY_URL, 'captcha response invalid or not found');
        }
        $recaptcha = $recaptchaByType[$captchaResponse['type']];
        $verify = $recaptcha->verify($captchaResponse['response']);
        if (!$verify->isSuccess()) {
            throw new UnauthorizedHttpException(ReCaptcha::SITE_VERIFY_URL, 'captcha response '.join($verify->getErrorCodes()));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            self::RECAPTCHA_EVENT => 'onJsonApiRecaptcha',
        ];
    }
}
