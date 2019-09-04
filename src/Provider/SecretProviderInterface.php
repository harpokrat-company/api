<?php

namespace App\Provider;

use App\Entity\Secret;
use Symfony\Component\Security\Core\User\UserInterface;

interface SecretProviderInterface
{
    public function createSecret(UserInterface $owner, $content): Secret;

    public function update(Secret $secret, $content): Secret;

    public function delete(Secret $secret);
}
