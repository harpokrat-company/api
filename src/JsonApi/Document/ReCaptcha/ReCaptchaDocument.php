<?php


namespace App\JsonApi\Document\ReCaptcha;


use WoohooLabs\Yin\JsonApi\Schema\Document\AbstractSimpleResourceDocument;
use WoohooLabs\Yin\JsonApi\Schema\JsonApiObject;
use WoohooLabs\Yin\JsonApi\Schema\Links;

class ReCaptchaDocument extends AbstractSimpleResourceDocument
{

    public function getJsonApi(): ?JsonApiObject
    {
        return new JsonApiObject('1.0');
    }

    public function getMeta(): array
    {
        return [];
    }

    public function getLinks(): ?Links
    {
        return null;
    }

    protected function getResource(): array
    {
        $attributes = [];
        foreach ($this->domainObject as $recaptcha) {
            $attributes[$recaptcha['type']] = [
                'type' => $recaptcha['type'],
                'siteKey' => $recaptcha['siteKey'],
            ];
        }

        return [
            'id' => '1',
            'type' => 'recaptcha',
            'attributes' => $attributes
        ];
    }
}
