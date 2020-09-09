<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends ResourceVoter
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    protected function getAttributesFunctions(): array
    {
        $self = function (User $subject, TokenInterface $token) {
            return $this->isUser($subject, $token);
        };
        $false = function (User $subject, TokenInterface $token) {
            return false;
        };

        return [
            'edit' => $self,
            'delete' => $false,
            'edit-vaults' => $self,
            'edit-secrets' => $self,
            'view-vaults' => $self,
            'view-logs' => $self,
        ];
    }

    private function isUser(User $subject, TokenInterface $token)
    {
        if (!$user = $token->getUser()) {
            return false;
        }

        return $user === $subject;
    }
}
