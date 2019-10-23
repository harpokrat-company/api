<?php


namespace App\Tests\functional;


use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait JsonWebTokenHelperTrait
{
    private function requestCreateJsonWebToken(KernelBrowser $client, string $email, string $password)
    {
        $client->request(
            'POST',
            '/v1/json-web-tokens',
            [],
            [],
            [
                'HTTP_Content-Type' => 'application/json',
                'HTTP_Authorization' => 'Basic ' . base64_encode($email . ':' . $password),
                'PHP_AUTH_USER' => $email,
                'PHP_AUTH_PW' => $password,
            ]
        );

        return $client->getResponse();
    }
}
