<?php


namespace App\Service\SecureActionHandler;


use App\Entity\SecureAction;

abstract class AbstractSecureActionHandler
{
    abstract function handleAction(SecureAction $action);
}
