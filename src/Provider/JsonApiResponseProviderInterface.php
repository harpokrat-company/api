<?php

namespace App\Provider;

use Symfony\Component\HttpFoundation\Response;

interface JsonApiResponseProviderInterface
{
    /**
     * @param null $data
     * @param null $errors
     * @param null $meta
     * @param null $links
     * @param null $included
     *
     * @return Response
     */
    public function createResponse($data = null, $errors = null, $meta = null, $links = null, $included = null):
    Response;

    /**
     * @param null $status
     * @param null $message
     * @param null $errors
     * @param null $meta
     * @param null $links
     * @param null $included
     *
     * @return Response
     */
    public function createErrorResponse($status = null, $message = null, $errors = null, $meta = null, $links = null,
                                        $included = null): Response;
}
