<?php

namespace App\Provider;

use JMS\Serializer\SerializerInterface;

interface JsonApiResponseProviderInterface
{
    public function __construct(SerializerInterface $serializer);

    public function createErrorResponse($status = null, $message = null, $errors = null, $meta = null, $links = null, $included = null);
}
