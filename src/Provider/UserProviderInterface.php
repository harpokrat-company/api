<?php

namespace App\Provider;

use Symfony\Component\Security\Core\User\UserInterface;

interface UserProviderInterface
{
    public function createUser(string $email, string $password): UserInterface;
}
