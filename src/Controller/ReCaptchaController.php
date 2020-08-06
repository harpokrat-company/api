<?php


namespace App\Controller;


use App\JsonApi\Document\ReCaptchaDocument;
use Paknahad\JsonApiBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\LockedException;
use WoohooLabs\Yin\JsonApi\JsonApi;

/**
 * @Route("/v1/recaptcha")
 */
class ReCaptchaController extends Controller
{
    private $siteKey;

    private $enabled;

    /**
     * ReCaptchaController constructor.
     * @param JsonApi $jsonApi
     * @param string $siteKey
     * @param bool $enabled
     */
    public function __construct(JsonApi $jsonApi, string $siteKey, bool $enabled)
    {
        parent::__construct($jsonApi);
        $this->siteKey = $siteKey;
        $this->enabled = $enabled;
    }

    /**
     * @Route("", name="recaptcha", methods="GET")
     */
    public function siteKey() {
        if (!$this->enabled) {
            throw new LockedException();
        }
        return $this->jsonApi()->respond()->ok(
            new ReCaptchaDocument(),
            ['siteKey' => $this->siteKey]
        );
    }
}