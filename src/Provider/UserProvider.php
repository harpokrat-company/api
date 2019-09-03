<?php


namespace App\Provider;


use App\Entity\User;
use App\Service\EmailValidationService;
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

    /**
     * @var EmailValidationService
     */
    private $emailValidationService;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        EmailValidationService $emailValidationService
    )
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->emailValidationService = $emailValidationService;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return UserInterface
     * @throws \Exception
     */
    public function createUser(string $email, string $password): UserInterface
    {
        $user = new User();
        $user->setEmail($email);
        $password = $this->passwordEncoder->encodePassword($user, $password);
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->emailValidationService->sendValidationMail($user);
        return $user;
    }
}
