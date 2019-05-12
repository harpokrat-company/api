<?php

namespace App\Model;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonApiResponse extends JsonApiDocument
{
    public function __construct($data = null, $errors = null, $meta = null, $links = null, $included = null)
    {
        if (!is_null($errors))
            $this->setErrors($errors);
        elseif (!is_null($data)) {
            $this->setData($data);
            $this->setIncluded($included);
        } elseif (is_null($meta))
            throw new HttpException(500);
        $this->setMeta($meta);
        $this->setLinks($links);
    }
}
