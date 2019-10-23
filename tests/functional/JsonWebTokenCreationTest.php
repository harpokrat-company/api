<?php


namespace App\Tests\functional;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class JsonWebTokenCreationTest extends WebTestCase
{
    use UserHelperTrait;
    use JsonWebTokenHelperTrait;
    use JsonApiResourceHelperTrait;

    public function testCreateJsonWebTokenResponseIsOk()
    {
        $client = static::createClient();
        $email = 'test@harpokrat.com';
        $password = 'qwerty1234';
        $response = $this->requestCreateUser($client, $email, $password);
        $userId = json_decode($response->getContent(), true)['data']['id'];
        $response = $this->requestCreateJsonWebToken($client, $email, $password);

        $this->assertSame(200, $response->getStatusCode());

        $data = $this->assertContentIsWellFormedJsonApiResource($response->getContent(), 'json-web-tokens');
        $this->assertArrayHasKey('attributes', $data);
        $attributes = $data['attributes'];
        $this->assertIsArray($attributes);
        $this->assertArrayHasKey('token', $attributes);
        $this->assertIsString($attributes['token']);

        $this->assertArrayHasKey('relationships', $data);
        $this->assertIsArray($data['relationships']);
        $this->assertArrayHasKey('user', $data['relationships']);
        $this->assertIsArray($data['relationships']['user']);
        $this->assertArrayHasKey('data', $data['relationships']['user']);
        $this->assertIsArray($data['relationships']['user']['data']);
        $this->assertArrayHasKey('type', $data['relationships']['user']['data']);
        $this->assertArrayHasKey('id', $data['relationships']['user']['data']);
        $this->assertSame($userId, $data['relationships']['user']['data']['id']);
        $this->assertSame('users', $data['relationships']['user']['data']['type']);
    }

    public function testCreateJsonWebTokenContentIsOk()
    {
        $client = static::createClient();
        $email = 'test@harpokrat.com';
        $password = 'qwerty1234';
        $this->requestCreateUser($client, $email, $password);
        $response = $this->requestCreateJsonWebToken($client, $email, $password);

        $this->assertSame(200, $response->getStatusCode());

        $token = json_decode($response->getContent(), true)['data']['attributes']['token'];
        $this->assertStringMatchesFormat('%s.%s.%s', $token);
        $jwtParts = explode('.', $token);
        $header = base64_decode($jwtParts[0]);
        $this->assertJson($header);
        $header = json_decode($header, true);
        $this->assertSame(
            [
                'typ'=> 'JWT',
                'alg'=> 'RS256',
            ],
            $header
        );
        $payload = base64_decode($jwtParts[1]);
        $this->assertJson($payload);
        $payload = json_decode($payload, true);
        $this->assertArrayHasKey('iat', $payload);
        $this->assertArrayHasKey('exp', $payload);
        $this->assertArrayHasKey('username', $payload);
        $this->assertArrayHasKey('jti', $payload);
        $this->assertLessThanOrEqual(time(), $payload['iat']);
        $this->assertGreaterThanOrEqual(time() + 1800, $payload['exp']);
        $this->assertLessThanOrEqual(time() + 3600, $payload['exp']);
        $this->assertSame($email, $payload['username']);
        $this->assertIsUuidV4($payload['jti']);
    }

    // TODO Check JWT signature
    // TODO Check JWT validation (maybe not here)
}
