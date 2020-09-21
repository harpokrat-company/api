<?php

namespace App\Controller;

use App\JsonApi\Document\ReCaptcha\ReCaptchaDocument;
use Paknahad\JsonApiBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use WoohooLabs\Yin\JsonApi\JsonApi;

/**
 * @Route("/v1/recaptcha")
 */
class ReCaptchaController extends Controller
{
    private $siteKeys;

    private $enabled;

    /**
     * ReCaptchaController constructor.
     */
    public function __construct(JsonApi $jsonApi, array $siteKeys, bool $enabled)
    {
        parent::__construct($jsonApi);
        $this->siteKeys = $siteKeys;
        $this->enabled = $enabled;
    }

    /**
     * @Route("", name="recaptcha", methods="GET")
     */
    public function siteKey()
    {
        return $this->jsonApi()->respond()->ok(
            new ReCaptchaDocument(),
            $this->siteKeys
        );
    }
}
