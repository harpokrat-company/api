<?php

namespace App\Entity;

use App\Entity\SecretOwnership\SecretOwnerInterface;
use App\Entity\SecretOwnership\SecretOwnerTrait;
use App\Entity\SecretOwnership\UserSecretOwnership;
use App\Entity\SecretOwnership\VaultSecretOwnership;
use App\Entity\VaultOwnership\VaultOwnerInterface;
use App\Entity\VaultOwnership\VaultOwnership;
use App\Validator\Constraints\EncryptionKey;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VaultRepository")
 * @EncryptionKey()
 */
class Vault implements SecretOwnerInterface, EncryptionKeyInterface
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
     * @ORM\Column(type="string", length=180)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var UserSecretOwnership
     * @ORM\OneToOne(targetEntity="App\Entity\SecretOwnership\VaultSecretOwnership", mappedBy="vault", cascade={"persist"})
     */
    private $secretOwnership;

    /**
     * @var VaultOwnership
     * @ORM\ManyToOne(targetEntity="App\Entity\VaultOwnership\VaultOwnership", inversedBy="vaults", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Assert\NotNull()
     */
    private $owner;

    /**
     * @var ?Secret
     * @ORM\OneToOne(targetEntity="App\Entity\Secret")
     * @ORM\JoinColumn(nullable=true)
     */
    private $encryptionKey;

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

    public function getOwner(): ?VaultOwnerInterface
    {
        return $this->owner->getOwner();
    }

    public function setOwner(?VaultOwnerInterface $owner): self
    {
        $this->owner = $owner->getVaultOwnership();

        return $this;
    }

    public function getEncryptionKey(): ?Secret
    {
        return $this->encryptionKey;
    }

    public function setEncryptionKey(?Secret $encryptionKey): self
    {
        $this->encryptionKey = $encryptionKey;

        return $this;
    }
}
