<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class MFAInvalidTokenException extends AuthenticationException
{
    public function getMessageKey()
    {
        return 'MFA not validated';
    }
}
