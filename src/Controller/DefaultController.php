<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractJsonApiController
{
    public function index(Request $request)
    {
        return new JsonResponse(['aled'=> 'oskour']);
    }
}
