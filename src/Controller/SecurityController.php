<?php

namespace App\Controller;

use App\JsonApi\Document\JsonWebToken\JsonWebTokenDocument;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Paknahad\JsonApiBundle\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/v1/json-web-tokens")
 */
class SecurityController extends Controller
{
    /**
     * @Route("", name="json-web-tokens_new", methods="POST")
     *
     * @return ResponseInterface
     *
     * @throws \Exception
     */
    public function createJsonWebToken(Request $request, JWTTokenManagerInterface $jwtManager)
    {
        // TODO use request body to set jwt jti / user if logas
        // TODO use user id instead of email (check in jwt.io) to prevent deletion / recreation of account with jwt
        // still valid
        $jti = Uuid::uuid4()->toString();
        $request->attributes->set('jti', $jti);

        return $this->jsonApi()->respond()->ok(
            new JsonWebTokenDocument($jwtManager),
            [
                'user' => $this->getUser(),
                'jti' => $jti,
            ]
        );
    }
}
