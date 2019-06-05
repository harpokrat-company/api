<?php


namespace App\Provider;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createUser(string $email, string $password): UserInterface
    {
        $user = new User();
        $user->setEmail($email);
        $password = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }
}
