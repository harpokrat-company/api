<?php


namespace App\Entity;

use App\Entity\SecretOwnership\SecretOwnerInterface;
use App\Entity\SecretOwnership\SecretOwnerTrait;
use App\Entity\SecretOwnership\UserSecretOwnership;
use App\Entity\SecretOwnership\VaultSecretOwnership;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VaultRepository")
 */
class Vault implements SecretOwnerInterface
{
    use SecretOwnerTrait;

    /**
     * @var UuidInterface
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var UserSecretOwnership
     * @ORM\OneToOne(targetEntity="App\Entity\SecretOwnership\VaultSecretOwnership", mappedBy="vault", cascade={"persist"})
     */
    private $secretOwnership;

    public function __construct()
    {
        $this->secretOwnership = new VaultSecretOwnership($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}