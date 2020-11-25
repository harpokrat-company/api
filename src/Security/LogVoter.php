<?php

namespace App\Security;

use App\Entity\Log;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LogVoter extends ResourceVoter
{
    public function __construct()
    {
        parent::__construct(Log::class);
    }

    protected function getAttributesFunctions(): array
    {
        $owner = function (Log $subject, TokenInterface $token) {
            return $subject->getUser() === $token->getUser();
        };
        $false = function (Log $subject, TokenInterface $token) {
            return false;
        };

        return [
            'view' => $owner,
            'edit' => $false,
            'delete' => $false,
        ];
    }
}
