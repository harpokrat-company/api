<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class JsonWebTokenListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $jti = $this->requestStack->getCurrentRequest()->attributes->get('jti');
        $mfaSecureAction = $this->requestStack->getCurrentRequest()->attributes->get('mfa-secure-action');

        $payload['jti'] = $jti;
        $payload['mfa-secure-action'] = $mfaSecureAction;

        $event->setData($payload);
    }
}
