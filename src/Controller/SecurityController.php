<?php

namespace App\Controller;

use App\Entity\SecureAction;
use App\JsonApi\Document\JsonWebToken\JsonWebTokenDocument;
use App\Provider\SecureActionProvider;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Paknahad\JsonApiBundle\Controller\Controller;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use WoohooLabs\Yin\JsonApi\JsonApi;

/**
 * @Route("/v1/json-web-tokens")
 */
class SecurityController extends Controller
{
    /**
     * @var SecureActionProvider
     */
    private $secureActionProvider;

    public function __construct(JsonApi $jsonApi, SecureActionProvider $secureActionProvider)
    {
        parent::__construct($jsonApi);
        $this->secureActionProvider = $secureActionProvider;
    }

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

        $mfaSecureAction = null;
        $user = $this->getUser();
        if ($user->isMfaActivated()) {
            $mfaSecureAction = new SecureAction();
            $this->secureActionProvider->mfaRegister($user, $mfaSecureAction);
            $request->attributes->set('mfa-secure-action', $mfaSecureAction->getId());
        }

        return $this->jsonApi()->respond()->ok(
            new JsonWebTokenDocument($jwtManager),
            [
                'user' => $user,
                'jti' => $jti,
                'mfa-secure-action' => $mfaSecureAction->getId(),
            ]
        );
    }
}
