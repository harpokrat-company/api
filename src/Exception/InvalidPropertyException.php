<?php

namespace App\Exception;

use WoohooLabs\Yin\JsonApi\Exception\AbstractJsonApiException;
use WoohooLabs\Yin\JsonApi\Schema\Error\Error;

class InvalidPropertyException extends AbstractJsonApiException
{
    public function __construct(string $propertyName)
    {
        parent::__construct("The property '".$propertyName."' cannot be edited.");
    }

    protected function getErrors(): array
    {
        return [
            Error::create()
                ->setStatus('400')
                ->setCode('CANNOT_EDIT_PROPERTY')
                ->setTitle('The property cannot be edited')
                ->setDetail($this->getMessage()),
        ];
    }
}
