<?php

namespace App\Service\SecureActionHandler;

use App\Entity\SecureAction;
use Doctrine\ORM\UnitOfWork;

class MfaHandler extends AbstractSecureActionHandler
{
    public function handleAction(SecureAction $action, UnitOfWork $unitOfWork)
    {
        // TODO: Implement handleAction() method.
    }
}
