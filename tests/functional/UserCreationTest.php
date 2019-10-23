<?php


namespace App\Tests\functional;


use App\Entity\SecureAction;
use Doctrine\ORM\EntityManager;
use Faker\Factory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserCreationTest extends WebTestCase
{
    use UserHelperTrait;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function setUp()
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testUserCreateResponseIsOk()
    {
        $client = static::createClient();
        $fakerFactory = Factory::create();
        $email = $fakerFactory->email;
        $password = $fakerFactory->password;
        $response = $this->requestCreateUser($client, $email, $password);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);

        $this->assertArrayHasKey('data', $content);
        $data = $content['data'];
        $this->assertArrayHasKey('type', $data);
        $this->assertSame('users', $data['type']);
        $this->assertArrayHasKey('id', $data);
        $this->assertStringMatchesFormat('%x-%x-%x-%x-%x', $data['id']);
        $this->assertArrayHasKey('attributes', $data);
        $attributes = $data['attributes'];
        $this->assertIsArray($attributes);
        $this->assertArrayHasKey('email', $attributes);
        $this->assertSame($email, $attributes['email']);
        $this->assertArrayHasKey('emailAddressValidated', $attributes);
        $this->assertFalse($attributes['emailAddressValidated']);
        $this->assertArrayNotHasKey('password', $attributes);
    }

    public function testUserCreateSendMailIsOk()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $fakerFactory = Factory::create();
        $email = $fakerFactory->email;
        $password = $fakerFactory->password;
        $this->requestCreateUser($client, $email, $password);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());

        $message = $mailCollector->getMessages()[0];
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertContains('email address validation', $message->getSubject());
        $this->assertSame('noreply@harpokrat.com', key($message->getFrom()));
        $this->assertSame($email, key($message->getTo()));
    }

    public function testUserCrateCreateSecureAction()
    {
        $client = static::createClient();
        $client->enableProfiler();
        $fakerFactory = Factory::create();
        $email = $fakerFactory->email;
        $password = $fakerFactory->password;
        $secureActionRepository = $this->entityManager->getRepository(SecureAction::class);

        $this->assertSame(0, $secureActionRepository->count([]));

        $this->requestCreateUser($client, $email, $password);
        $message = $client->getProfile()->getCollector('swiftmailer')->getMessages()[0];

        $this->assertSame(1, $secureActionRepository->count([]));

        /** @var SecureAction $secureAction */
        $secureAction = $secureActionRepository->findAll()[0];
        $validationLink =
            'https://www.harpokrat.com/secure-action/?id='
            . $secureAction->getId()
            . '&amp;token='
            . $secureAction->getToken();
        $this->assertContains($validationLink, $message->getBody());
        $this->assertSame(SecureAction::ACTION_VALIDATE_EMAIL_ADDRESS, $secureAction->getType());
    }
}
