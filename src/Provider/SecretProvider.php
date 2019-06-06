<?php


namespace App\Provider;

use App\Entity\Secret;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SecretProvider implements SecretProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createSecret(UserInterface $owner, $content): Secret
    {
        $secret = new Secret();
        $secret->setOwner($owner);
        $secret->setContent($content);
        $this->entityManager->persist($secret);
        $this->entityManager->flush();
        return $secret;
    }

    public function update(Secret $secret, $content): Secret
    {
        $secret->setContent($content);
        $this->entityManager->persist($secret);
        $this->entityManager->flush();
        return $secret;
    }

    public function delete(Secret $secret)
    {
        $this->entityManager->remove($secret);
        $this->entityManager->flush();
    }
}
