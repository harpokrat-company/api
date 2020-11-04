<?php


namespace App\Exception;


use WoohooLabs\Yin\JsonApi\Exception\AbstractJsonApiException;
use WoohooLabs\Yin\JsonApi\Schema\Error\Error;

class PropertyAccessDeniedException extends AbstractJsonApiException
{
    public function __construct(string $propertyName)
    {
        parent::__construct("You are not authorized to edit the property '".$propertyName."'.");
    }

    protected function getErrors(): array
    {
        return [
            Error::create()
                ->setStatus('401')
                ->setCode('PROPERTY_ACCESS_DENIED')
                ->setTitle('You are not authorized to edit this property')
                ->setDetail($this->getMessage()),
        ];
    }
}