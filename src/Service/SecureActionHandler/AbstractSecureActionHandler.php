<?php

namespace App\Service\SecureActionHandler;

use App\Entity\SecureAction;
use Doctrine\ORM\UnitOfWork;

abstract class AbstractSecureActionHandler
{
    abstract public function handleAction(SecureAction $action, UnitOfWork $unitOfWork);
}
