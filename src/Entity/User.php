<?php

namespace App\Entity;

use App\Entity\SecretOwnership\SecretOwnerInterface;
use App\Entity\SecretOwnership\SecretOwnerTrait;
use App\Entity\SecretOwnership\UserSecretOwnership;
use App\Entity\VaultOwnership\UserVaultOwnership;
use App\Entity\VaultOwnership\VaultOwnerInterface;
use App\Entity\VaultOwnership\VaultOwnerTrait;
use App\Validator\Constraints\EncryptionKey;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 * @EncryptionKey()
 */
class User implements UserInterface, SecretOwnerInterface, VaultOwnerInterface, EncryptionKeyInterface
{
    use SecretOwnerTrait;
    use VaultOwnerTrait;

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
     * @Assert\Email()
     */
    private $email;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="8",
     *     max="4096"
     * )
     */
    private $password;

    /**
     * @var UserSecretOwnership
     * @ORM\OneToOne(targetEntity="App\Entity\SecretOwnership\UserSecretOwnership", mappedBy="user", cascade={"persist", "remove"})
     */
    private $secretOwnership;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="App\Entity\Log", mappedBy="user")
     */
    private $logs;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $emailAddressValidated;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $mfaActivated = false;

    /**
     * @var array
     * @ORM\ManyToMany(targetEntity="App\Entity\Organization", mappedBy="members")
     */
    private $organizations;

    /**
     * @var array
     * @ORM\OneToMany(targetEntity="App\Entity\Organization", mappedBy="owner")
     */
    private $ownedOrganizations;

    /**
     * @var UserVaultOwnership
     * @ORM\OneToOne(targetEntity="App\Entity\VaultOwnership\UserVaultOwnership", mappedBy="user", cascade={"persist", "remove"})
     */
    private $vaultOwnership;

    /**
     * @var ?Secret
     * @ORM\OneToOne(targetEntity="App\Entity\Secret")
     * @ORM\JoinColumn(nullable=true)
     */
    private $encryptionKey;

    public function __construct()
    {
        $this->logs = new ArrayCollection();
        $this->emailAddressValidated = false;
        $this->organizations = new ArrayCollection();
        $this->ownedOrganizations = new ArrayCollection();
        $this->secretOwnership = new UserSecretOwnership($this);
        $this->vaultOwnership = new UserVaultOwnership($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(?string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(?string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection|Log[]
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): self
    {
        if (!$this->logs->contains($log)) {
            $this->logs[] = $log;
            $log->setUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): self
    {
        if ($this->logs->contains($log)) {
            $this->logs->removeElement($log);
            // set the owning side to null (unless already changed)
            if ($log->getUser() === $this) {
                $log->setUser(null);
            }
        }

        return $this;
    }

    public function isEmailAddressValidated(): bool
    {
        return $this->emailAddressValidated;
    }

    public function setEmailAddressValidated(bool $emailAddressValidated): User
    {
        $this->emailAddressValidated = $emailAddressValidated;

        return $this;
    }

    public function isMfaActivated(): bool
    {
        return $this->mfaActivated;
    }

    public function setMfaActivated(bool $mfaActivated): void
    {
        $this->mfaActivated = $mfaActivated;
    }

    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function addOrganization(Organization $organization): self
    {
        if (!$this->organizations->contains($organization)) {
            $this->organizations[] = $organization;
        }

        return $this;
    }

    public function removeOrganization(Organization $organization): self
    {
        if ($this->organizations->contains($organization)) {
            $this->organizations->removeElement($organization);
        }

        return $this;
    }

    public function getOwnedOrganizations(): Collection
    {
        return $this->ownedOrganizations;
    }

    public function addOwnedOrganization(Organization $organization): self
    {
        if (!$this->ownedOrganizations->contains($organization)) {
            $this->ownedOrganizations[] = $organization;
        }

        return $this;
    }

    public function removeOwnedOrganization(Organization $organization): self
    {
        if ($this->ownedOrganizations->contains($organization)) {
            $this->ownedOrganizations->removeElement($organization);
        }

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
