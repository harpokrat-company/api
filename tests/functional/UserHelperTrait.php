<?php


namespace App\Tests\functional;


use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait UserHelperTrait
{
    private function requestCreateUser(KernelBrowser $client, string $email, string $password, array $attributes = [])
    {
        $attributes['email'] = $email;
        $attributes['password'] = $password;
        $client->request(
            'POST',
            '/v1/users',
            [],
            [],
            ['HTTP_Content-Type' => 'application/json'],
            json_encode([
                'data' => [
                    'type' => 'users',
                    'attributes' => $attributes,
                ],
            ]));

        return $client->getResponse();
    }
}
