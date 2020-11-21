<?php

namespace App\Guard;

use App\Entity\SecureAction;
use App\Exception\MFAInvalidTokenException;
use App\Repository\SecureActionRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class JWTMfaTokenAuthenticator extends JWTTokenAuthenticator
{
    /**
     * @var SecureActionRepository
     */
    private $secureActionRepository;

    public function __construct(
        JWTTokenManagerInterface $jwtManager,
        EventDispatcherInterface $dispatcher,
        TokenExtractorInterface $tokenExtractor,
        TokenStorageInterface $preAuthenticationTokenStorage,
        SecureActionRepository $secureActionRepository
    ) {
        parent::__construct($jwtManager, $dispatcher, $tokenExtractor, $preAuthenticationTokenStorage);
        $this->secureActionRepository = $secureActionRepository;
    }

    public function getCredentials(Request $request)
    {
        $preAuthToken = parent::getCredentials($request);

        $payload = $preAuthToken->getPayload();
        if (
            \array_key_exists('mfa-secure-action', $payload)
            && null !== ($mfaSecureAction = $payload['mfa-secure-action'])
        ) {
            /** @var SecureAction $secureAction */
            $secureAction = $this->secureActionRepository->find($mfaSecureAction);
            if (!$secureAction->getValidated()) {
                throw new MFAInvalidTokenException();
            }
        }

        return $preAuthToken;
    }
}
