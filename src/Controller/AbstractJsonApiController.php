<?php


namespace App\Controller;


use App\Provider\JsonApiResponseProviderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbstractJsonApiController extends AbstractController
{
    /**
     * @var JsonApiResponseProviderInterface
     */
    protected $jsonApiResponseProvider;

    public function __construct(JsonApiResponseProviderInterface $jsonApiResponseProvider)
    {
        $this->jsonApiResponseProvider = $jsonApiResponseProvider;
    }
}
