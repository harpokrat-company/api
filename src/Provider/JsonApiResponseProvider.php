<?php


namespace App\Provider;


use App\Model\JsonApiError;
use App\Model\JsonApiErrorInterface;
use App\Model\JsonApiResponse;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonApiResponseProvider implements JsonApiResponseProviderInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    private function getStatusFromErrors($errors = null)
    {
        if (empty($errors))
            return 200;
        return 500; // TODO
    }

    public function createResponse($data = null, $errors = null, $meta = null, $links = null, $included = null)
    {
        $jsonApiResponse = new JsonApiResponse($data, $errors, $meta, $links, $included);
        return new Response(
            $this->serializer->serialize($jsonApiResponse, 'json'),
            $this->getStatusFromErrors($errors),
            [
                'Content-Type' => 'application/vnd.api+json',
            ]
        );
    }

    public function createErrorResponse($status = null, $message = null, $errors = null, $meta = null, $links = null,
                                        $included = null)
    {
        if (is_null($errors)) {
            if (is_array($status)) {
                $errors = $status;
                $status = null;
            } elseif (is_null($status))
                throw new HttpException(500);
            else {
                $errors = [
                    new JsonApiError($status, $message),
                ];
            }
        }
        return $this->createResponse(null, $errors, $meta, $links, $included);
    }
}
